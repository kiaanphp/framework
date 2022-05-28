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
namespace Kiaan\Contact\Socket\Server;

/*
|---------------------------------------------------
| Uses
|---------------------------------------------------
*/
use Kiaan\Contact\Socket\MessageComponentInterface;
use Kiaan\Contact\Socket\Addons\React\EventLoop\LoopInterface;
use Kiaan\Contact\Socket\Addons\React\Socket\ServerInterface;
use Kiaan\Contact\Socket\Addons\React\EventLoop\Factory as LoopFactory;
use Kiaan\Contact\Socket\Addons\React\Socket\Server as Reactor;
use Kiaan\Contact\Socket\Addons\React\Socket\SecureServer as SecureReactor;

/*
|---------------------------------------------------
| Class
|---------------------------------------------------
*/
class IoServer {
    /**
     * @var \React\EventLoop\LoopInterface
     */
    public $loop;

    /**
     * @var \Socket\MessageComponentInterface
     */
    public $app;

    /**
     * The socket server the Socket Application is run off of
     * @var \React\Socket\ServerInterface
     */
    public $socket;

    /**
     * @param \Socket\MessageComponentInterface  $app      The Socket application stack to host
     * @param \React\Socket\ServerInterface       $socket   The React socket server to run the Socket application off of
     * @param \React\EventLoop\LoopInterface|null $loop     The React looper to run the Socket application off of
     */
    public function __construct(MessageComponentInterface $app, ServerInterface $socket, LoopInterface $loop = null) {
        if (false === strpos(PHP_VERSION, "hiphop")) {
            gc_enable();
        }

        set_time_limit(0);
        ob_implicit_flush();

        $this->loop = $loop;
        $this->app  = $app;
        $this->socket = $socket;

        $socket->on('connection', array($this, 'handleConnect'));
    }

    /**
     * @param  \Socket\MessageComponentInterface $component  The application that I/O will call when events are received
     * @param  int                                $port       The port to server sockets on
     * @param  string                             $address    The address to receive sockets on (0.0.0.0 means receive connections from any)
     * @return IoServer
     */
    public static function factory(MessageComponentInterface $component, $port = 80, $address = '0.0.0.0') {
        $loop   = LoopFactory::create();
        $socket = new Reactor($address . ':' . $port, $loop);

        return new static($component, $socket, $loop);
    }

    /**
     * Run the application by entering the event loop
     * @throws \RuntimeException If a loop was not previously specified
     */
    public function run() {
        if (null === $this->loop) {
            throw new \RuntimeException("A React Loop was not provided during instantiation");
        }

        // @codeCoverageIgnoreStart
        $this->loop->run();
        // @codeCoverageIgnoreEnd
    }

    /**
     * Triggered when a new connection is received from React
     * @param \React\Socket\ConnectionInterface $conn
     */
    public function handleConnect($conn) {
        $conn->decor = new IoConnection($conn);
        $conn->decor->id = (int)$conn->stream;

        $uri = $conn->getRemoteAddress();
        $conn->decor->address = trim(
            parse_url((strpos($uri, '://') === false ? 'tcp://' : '') . $uri, PHP_URL_HOST),
            '[]'
        );

        $this->app->onOpen($conn->decor);

        $conn->on('data', function ($data) use ($conn) {
            $this->handleData($data, $conn);
        });
        $conn->on('close', function () use ($conn) {
            $this->handleEnd($conn);
        });
        $conn->on('error', function (\Exception $e) use ($conn) {
            $this->handleError($e, $conn);
        });
    }

    /**
     * Data has been received from React
     * @param string                            $data
     * @param \React\Socket\ConnectionInterface $conn
     */
    public function handleData($data, $conn) {
        try {
            $this->app->onMessage($conn->decor, $data);
        } catch (\Exception $e) {
            $this->handleError($e, $conn);
        }
    }

    /**
     * A connection has been closed by React
     * @param \React\Socket\ConnectionInterface $conn
     */
    public function handleEnd($conn) {
        try {
            $this->app->onClose($conn->decor);
        } catch (\Exception $e) {
            $this->handleError($e, $conn);
        }

        unset($conn->decor);
    }

    /**
     * An error has occurred, let the listening application know
     * @param \Exception                        $e
     * @param \React\Socket\ConnectionInterface $conn
     */
    public function handleError(\Exception $e, $conn) {
        $this->app->onError($conn->decor, $e);
    }
}
