<?php

/**
 * Kiaan framework
 *
 * @author Hassan Kerdash kerdashhassan@gmail.com
 **/

/*
|---------------------------------------------------
| Namespaces
|---------------------------------------------------
*/
namespace Kiaan\Contact\Socket\Addons\React\Dns\Query;

/*
|---------------------------------------------------
| Uses
|---------------------------------------------------
*/
use Kiaan\Contact\Socket\Addons\React\Dns\Model\Message;
use Kiaan\Contact\Socket\Addons\React\Dns\Protocol\BinaryDumper;
use Kiaan\Contact\Socket\Addons\React\Dns\Protocol\Parser;
use Kiaan\Contact\Socket\Addons\React\EventLoop\LoopInterface;
use Kiaan\Contact\Socket\Addons\React\Promise\Deferred;

/*
|---------------------------------------------------
| Class
|---------------------------------------------------
*/
class TcpTransportExecutor implements ExecutorInterface
{
    private $nameserver;
    private $loop;
    private $parser;
    private $dumper;

    /**
     * @var ?resource
     */
    private $socket;

    /**
     * @var Deferred[]
     */
    private $pending = array();

    /**
     * @var string[]
     */
    private $names = array();

    /**
     * Maximum idle time when socket is current unused (i.e. no pending queries outstanding)
     *
     * If a new query is to be sent during the idle period, we can reuse the
     * existing socket without having to wait for a new socket connection.
     * This uses a rather small, hard-coded value to not keep any unneeded
     * sockets open and to not keep the loop busy longer than needed.
     *
     * A future implementation may take advantage of `edns-tcp-keepalive` to keep
     * the socket open for longer periods. This will likely require explicit
     * configuration because this may consume additional resources and also keep
     * the loop busy for longer than expected in some applications.
     *
     * @var float
     * @link https://tools.ietf.org/html/rfc7766#section-6.2.1
     * @link https://tools.ietf.org/html/rfc7828
     */
    private $idlePeriod = 0.001;

    /**
     * @var ?\React\EventLoop\TimerInterface
     */
    private $idleTimer;

    private $writeBuffer = '';
    private $writePending = false;

    private $readBuffer = '';
    private $readPending = false;

    /**
     * @param string        $nameserver
     * @param LoopInterface $loop
     */
    public function __construct($nameserver, LoopInterface $loop)
    {
        if (\strpos($nameserver, '[') === false && \substr_count($nameserver, ':') >= 2 && \strpos($nameserver, '://') === false) {
            // several colons, but not enclosed in square brackets => enclose IPv6 address in square brackets
            $nameserver = '[' . $nameserver . ']';
        }

        $parts = \parse_url((\strpos($nameserver, '://') === false ? 'tcp://' : '') . $nameserver);
        if (!isset($parts['scheme'], $parts['host']) || $parts['scheme'] !== 'tcp' || !\filter_var(\trim($parts['host'], '[]'), \FILTER_VALIDATE_IP)) {
            throw new \InvalidArgumentException('Invalid nameserver address given');
        }

        $this->nameserver = $parts['host'] . ':' . (isset($parts['port']) ? $parts['port'] : 53);
        $this->loop = $loop;
        $this->parser = new Parser();
        $this->dumper = new BinaryDumper();
    }

    public function query(Query $query)
    {
        $request = Message::createRequestForQuery($query);

        // keep shuffing message ID to avoid using the same message ID for two pending queries at the same time
        while (isset($this->pending[$request->id])) {
            $request->id = \mt_rand(0, 0xffff); // @codeCoverageIgnore
        }

        $queryData = $this->dumper->toBinary($request);
        $length = \strlen($queryData);
        if ($length > 0xffff) {
            return \React\Promise\reject(new \RuntimeException(
                'DNS query for ' . $query->name . ' failed: Query too large for TCP transport'
            ));
        }

        $queryData = \pack('n', $length) . $queryData;

        if ($this->socket === null) {
            // create async TCP/IP connection (may take a while)
            $socket = @\stream_socket_client($this->nameserver, $errno, $errstr, 0, \STREAM_CLIENT_CONNECT | \STREAM_CLIENT_ASYNC_CONNECT);
            if ($socket === false) {
                return \React\Promise\reject(new \RuntimeException(
                    'DNS query for ' . $query->name . ' failed: Unable to connect to DNS server ('  . $errstr . ')',
                    $errno
                ));
            }

            // set socket to non-blocking and wait for it to become writable (connection success/rejected)
            \stream_set_blocking($socket, false);
            $this->socket = $socket;
        }

        if ($this->idleTimer !== null) {
            $this->loop->cancelTimer($this->idleTimer);
            $this->idleTimer = null;
        }

        // wait for socket to become writable to actually write out data
        $this->writeBuffer .= $queryData;
        if (!$this->writePending) {
            $this->writePending = true;
            $this->loop->addWriteStream($this->socket, array($this, 'handleWritable'));
        }

        $names =& $this->names;
        $that = $this;
        $deferred = new Deferred(function () use ($that, &$names, $request) {
            // remove from list of pending names, but remember pending query
            $name = $names[$request->id];
            unset($names[$request->id]);
            $that->checkIdle();

            throw new CancellationException('DNS query for ' . $name . ' has been cancelled');
        });

        $this->pending[$request->id] = $deferred;
        $this->names[$request->id] = $query->name;

        return $deferred->promise();
    }

    /**
     * @internal
     */
    public function handleWritable()
    {
        if ($this->readPending === false) {
            $name = @\stream_socket_get_name($this->socket, true);
            if ($name === false) {
                $this->closeError('Connection to DNS server rejected');
                return;
            }

            $this->readPending = true;
            $this->loop->addReadStream($this->socket, array($this, 'handleRead'));
        }

        $written = @\fwrite($this->socket, $this->writeBuffer);
        if ($written === false || $written === 0) {
            $this->closeError('Unable to write to closed socket');
            return;
        }

        if (isset($this->writeBuffer[$written])) {
            $this->writeBuffer = \substr($this->writeBuffer, $written);
        } else {
            $this->loop->removeWriteStream($this->socket);
            $this->writePending = false;
            $this->writeBuffer = '';
        }
    }

    /**
     * @internal
     */
    public function handleRead()
    {
        // read one chunk of data from the DNS server
        // any error is fatal, this is a stream of TCP/IP data
        $chunk = @\fread($this->socket, 65536);
        if ($chunk === false || $chunk === '') {
            $this->closeError('Connection to DNS server lost');
            return;
        }

        // reassemble complete message by concatenating all chunks.
        $this->readBuffer .= $chunk;

        // response message header contains at least 12 bytes
        while (isset($this->readBuffer[11])) {
            // read response message length from first 2 bytes and ensure we have length + data in buffer
            list(, $length) = \unpack('n', $this->readBuffer);
            if (!isset($this->readBuffer[$length + 1])) {
                return;
            }

            $data = \substr($this->readBuffer, 2, $length);
            $this->readBuffer = (string)substr($this->readBuffer, $length + 2);

            try {
                $response = $this->parser->parseMessage($data);
            } catch (\Exception $e) {
                // reject all pending queries if we received an invalid message from remote server
                $this->closeError('Invalid message received from DNS server');
                return;
            }

            // reject all pending queries if we received an unexpected response ID or truncated response
            if (!isset($this->pending[$response->id]) || $response->tc) {
                $this->closeError('Invalid response message received from DNS server');
                return;
            }

            $deferred = $this->pending[$response->id];
            unset($this->pending[$response->id], $this->names[$response->id]);

            $deferred->resolve($response);

            $this->checkIdle();
        }
    }

    /**
     * @internal
     * @param string $reason
     */
    public function closeError($reason)
    {
        $this->readBuffer = '';
        if ($this->readPending) {
            $this->loop->removeReadStream($this->socket);
            $this->readPending = false;
        }

        $this->writeBuffer = '';
        if ($this->writePending) {
            $this->loop->removeWriteStream($this->socket);
            $this->writePending = false;
        }

        if ($this->idleTimer !== null) {
            $this->loop->cancelTimer($this->idleTimer);
            $this->idleTimer = null;
        }

        @\fclose($this->socket);
        $this->socket = null;

        foreach ($this->names as $id => $name) {
            $this->pending[$id]->reject(new \RuntimeException(
                'DNS query for ' . $name . ' failed: ' . $reason
            ));
        }
        $this->pending = $this->names = array();
    }

    /**
     * @internal
     */
    public function checkIdle()
    {
        if ($this->idleTimer === null && !$this->names) {
            $that = $this;
            $this->idleTimer = $this->loop->addTimer($this->idlePeriod, function () use ($that) {
                $that->closeError('Idle timeout');
            });
        }
    }
}
