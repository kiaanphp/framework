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
namespace Kiaan\Contact\Socket\WebSocket;

/*
|---------------------------------------------------
| Uses
|---------------------------------------------------
*/
use Kiaan\Contact\Socket\ComponentInterface;
use Kiaan\Contact\Socket\ConnectionInterface;
use Kiaan\Contact\Socket\MessageComponentInterface as DataComponentInterface;
use Kiaan\Contact\Socket\Http\HttpServerInterface;
use Kiaan\Contact\Socket\Http\CloseResponseTrait;
use Kiaan\Contact\Socket\Addons\Psr\RequestInterface;
use Kiaan\Contact\Socket\RFC6455\Messaging\MessageInterface;
use Kiaan\Contact\Socket\RFC6455\Messaging\FrameInterface;
use Kiaan\Contact\Socket\RFC6455\Messaging\Frame;
use Kiaan\Contact\Socket\RFC6455\Messaging\MessageBuffer;
use Kiaan\Contact\Socket\RFC6455\Messaging\CloseFrameChecker;
use Kiaan\Contact\Socket\RFC6455\Handshake\ServerNegotiator;
use Kiaan\Contact\Socket\RFC6455\Handshake\RequestVerifier;
use Kiaan\Contact\Socket\Addons\React\EventLoop\LoopInterface;
use Kiaan\Contact\Socket\Addons\Psr7\Functions as gPsr;

/*
|---------------------------------------------------
| Class
|---------------------------------------------------
*/
class WsServer implements HttpServerInterface {
    use CloseResponseTrait;

    /**
     * Decorated component
     * @var \Socket\ComponentInterface
     */
    private $delegate;

    /**
     * @var \SplObjectStorage
     */
    protected $connections;

    /**
     * @var \Socket\RFC6455\Messaging\CloseFrameChecker
     */
    private $closeFrameChecker;

    /**
     * @var \Socket\RFC6455\Handshake\ServerNegotiator
     */
    private $handshakeNegotiator;

    /**
     * @var \Closure
     */
    private $ueFlowFactory;

    /**
     * @var \Closure
     */
    private $pongReceiver;

    /**
     * @var \Closure
     */
    private $msgCb;

    /**
     */
    public function __construct(ComponentInterface $component) {
        if ($component instanceof MessageComponentInterface) {
            $this->msgCb = function(ConnectionInterface $conn, MessageInterface $msg) {
                $this->delegate->onMessage($conn, $msg);
            };
        } elseif ($component instanceof DataComponentInterface) {
            $this->msgCb = function(ConnectionInterface $conn, MessageInterface $msg) {
                $this->delegate->onMessage($conn, $msg->getPayload());
            };
        } else {
            throw new \UnexpectedValueException('Expected instance of \Socket\WebSocket\MessageComponentInterface or \Socket\MessageComponentInterface');
        }

        if (bin2hex('✓') !== 'e29c93') {
            throw new \DomainException('Bad encoding, unicode character ✓ did not match expected value. Ensure charset UTF-8 and check ini val mbstring.func_autoload');
        }

        $this->delegate    = $component;
        $this->connections = new \SplObjectStorage;

        $this->closeFrameChecker   = new CloseFrameChecker;
        $this->handshakeNegotiator = new ServerNegotiator(new RequestVerifier);
        $this->handshakeNegotiator->setStrictSubProtocolCheck(true);

        if ($component instanceof WsServerInterface) {
            $this->handshakeNegotiator->setSupportedSubProtocols($component->getSubProtocols());
        }

        $this->pongReceiver = function() {};

        $reusableUnderflowException = new \UnderflowException;
        $this->ueFlowFactory = function() use ($reusableUnderflowException) {
            return $reusableUnderflowException;
        };
    }

    /**
     * {@inheritdoc}
     */
    public function onOpen(ConnectionInterface $conn, RequestInterface $request = null) {
        if (null === $request) {
            throw new \UnexpectedValueException('$request can not be null');
        }

        $conn->httpRequest = $request;

        $conn->WebSocket            = new \StdClass;
        $conn->WebSocket->closing   = false;

        $response = $this->handshakeNegotiator->handshake($request)->withHeader('X-Powered-By', 'Socket');

        $conn->send(gPsr::str($response));

        if (101 !== $response->getStatusCode()) {
            return $conn->close();
        }

        $wsConn = new WsConnection($conn);

        $streamer = new MessageBuffer(
            $this->closeFrameChecker,
            function(MessageInterface $msg) use ($wsConn) {
                $cb = $this->msgCb;
                $cb($wsConn, $msg);
            },
            function(FrameInterface $frame) use ($wsConn) {
                $this->onControlFrame($frame, $wsConn);
            },
            true,
            $this->ueFlowFactory
        );

        $this->connections->attach($conn, new ConnContext($wsConn, $streamer));

        return $this->delegate->onOpen($wsConn);
    }

    /**
     * {@inheritdoc}
     */
    public function onMessage(ConnectionInterface $from, $msg) {
        if ($from->WebSocket->closing) {
            return;
        }

        $this->connections[$from]->buffer->onMessage($msg);
    }

    /**
     * {@inheritdoc}
     */
    public function onClose(ConnectionInterface $conn) {
        if ($this->connections->contains($conn)) {
            $context = $this->connections[$conn];
            $this->connections->detach($conn);

            $this->delegate->onClose($context->connection);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function onError(ConnectionInterface $conn, \Exception $e) {
        if ($this->connections->contains($conn)) {
            $this->delegate->onError($this->connections[$conn]->connection, $e);
        } else {
            $conn->close();
        }
    }

    public function onControlFrame(FrameInterface $frame, WsConnection $conn) {
        switch ($frame->getOpCode()) {
            case Frame::OP_CLOSE:
                $conn->close($frame);
                break;
            case Frame::OP_PING:
                $conn->send(new Frame($frame->getPayload(), true, Frame::OP_PONG));
                break;
            case Frame::OP_PONG:
                $pongReceiver = $this->pongReceiver;
                $pongReceiver($frame, $conn);
            break;
        }
    }

    public function setStrictSubProtocolCheck($enable) {
        $this->handshakeNegotiator->setStrictSubProtocolCheck($enable);
    }

    public function enableKeepAlive(LoopInterface $loop, $interval = 30) {
        $lastPing = new Frame(uniqid(), true, Frame::OP_PING);
        $pingedConnections = new \SplObjectStorage;
        $splClearer = new \SplObjectStorage;

        $this->pongReceiver = function(FrameInterface $frame, $wsConn) use ($pingedConnections, &$lastPing) {
            if ($frame->getPayload() === $lastPing->getPayload()) {
                $pingedConnections->detach($wsConn);
            }
        };

        $loop->addPeriodicTimer((int)$interval, function() use ($pingedConnections, &$lastPing, $splClearer) {
            foreach ($pingedConnections as $wsConn) {
                $wsConn->close();
            }
            $pingedConnections->removeAllExcept($splClearer);

            $lastPing = new Frame(uniqid(), true, Frame::OP_PING);

            foreach ($this->connections as $key => $conn) {
                $wsConn  = $this->connections[$conn]->connection;

                $wsConn->send($lastPing);
                $pingedConnections->attach($wsConn);
            }
        });
   }
}
