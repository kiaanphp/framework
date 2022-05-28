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
final class UdpTransportExecutor implements ExecutorInterface
{
    private $nameserver;
    private $loop;
    private $parser;
    private $dumper;

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

        $parts = \parse_url((\strpos($nameserver, '://') === false ? 'udp://' : '') . $nameserver);
        if (!isset($parts['scheme'], $parts['host']) || $parts['scheme'] !== 'udp' || !\filter_var(\trim($parts['host'], '[]'), \FILTER_VALIDATE_IP)) {
            throw new \InvalidArgumentException('Invalid nameserver address given');
        }

        $this->nameserver = 'udp://' . $parts['host'] . ':' . (isset($parts['port']) ? $parts['port'] : 53);
        $this->loop = $loop;
        $this->parser = new Parser();
        $this->dumper = new BinaryDumper();
    }

    public function query(Query $query)
    {
        $request = Message::createRequestForQuery($query);

        $queryData = $this->dumper->toBinary($request);
        if (isset($queryData[512])) {
            return \React\Promise\reject(new \RuntimeException(
                'DNS query for ' . $query->name . ' failed: Query too large for UDP transport',
                \defined('SOCKET_EMSGSIZE') ? \SOCKET_EMSGSIZE : 90
            ));
        }

        // UDP connections are instant, so try connection without a loop or timeout
        $socket = @\stream_socket_client($this->nameserver, $errno, $errstr, 0);
        if ($socket === false) {
            return \React\Promise\reject(new \RuntimeException(
                'DNS query for ' . $query->name . ' failed: Unable to connect to DNS server ('  . $errstr . ')',
                $errno
            ));
        }

        // set socket to non-blocking and immediately try to send (fill write buffer)
        \stream_set_blocking($socket, false);
        \fwrite($socket, $queryData);

        $loop = $this->loop;
        $deferred = new Deferred(function () use ($loop, $socket, $query) {
            // cancellation should remove socket from loop and close socket
            $loop->removeReadStream($socket);
            \fclose($socket);

            throw new CancellationException('DNS query for ' . $query->name . ' has been cancelled');
        });

        $parser = $this->parser;
        $loop->addReadStream($socket, function ($socket) use ($loop, $deferred, $query, $parser, $request) {
            // try to read a single data packet from the DNS server
            // ignoring any errors, this is uses UDP packets and not a stream of data
            $data = @\fread($socket, 512);

            try {
                $response = $parser->parseMessage($data);
            } catch (\Exception $e) {
                // ignore and await next if we received an invalid message from remote server
                // this may as well be a fake response from an attacker (possible DOS)
                return;
            }

            // ignore and await next if we received an unexpected response ID
            // this may as well be a fake response from an attacker (possible cache poisoning)
            if ($response->id !== $request->id) {
                return;
            }

            // we only react to the first valid message, so remove socket from loop and close
            $loop->removeReadStream($socket);
            \fclose($socket);

            if ($response->tc) {
                $deferred->reject(new \RuntimeException(
                    'DNS query for ' . $query->name . ' failed: The server returned a truncated result for a UDP query',
                    \defined('SOCKET_EMSGSIZE') ? \SOCKET_EMSGSIZE : 90
                ));
                return;
            }

            $deferred->resolve($response);
        });

        return $deferred->promise();
    }
}
