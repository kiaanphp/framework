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
namespace Kiaan\Contact\Socket\Http;

/*
|---------------------------------------------------
| Uses
|---------------------------------------------------
*/
use Kiaan\Contact\Socket\ConnectionInterface;
use Kiaan\Contact\Socket\MessageComponentInterface;
use Kiaan\Contact\Socket\Addons\Psr\RequestInterface;

/*
|---------------------------------------------------
| Class
|---------------------------------------------------
*/
class OriginCheck implements HttpServerInterface {
    use CloseResponseTrait;

    /**
     * @var \Socket\MessageComponentInterface
     */
    protected $_component;

    public $allowedOrigins = [];

    /**
     * @param MessageComponentInterface $component Component/Application to decorate
     * @param array                     $allowed   An array of allowed domains that are allowed to connect from
     */
    public function __construct(MessageComponentInterface $component, array $allowed = []) {
        $this->_component = $component;
        $this->allowedOrigins += $allowed;
    }

    /**
     * {@inheritdoc}
     */
    public function onOpen(ConnectionInterface $conn, RequestInterface $request = null) {
        $header = (string)$request->getHeader('Origin')[0];
        $origin = parse_url($header, PHP_URL_HOST) ?: $header;

        if (!in_array($origin, $this->allowedOrigins)) {
            return $this->close($conn, 403);
        }

        return $this->_component->onOpen($conn, $request);
    }

    /**
     * {@inheritdoc}
     */
    function onMessage(ConnectionInterface $from, $msg) {
        return $this->_component->onMessage($from, $msg);
    }

    /**
     * {@inheritdoc}
     */
    function onClose(ConnectionInterface $conn) {
        return $this->_component->onClose($conn);
    }

    /**
     * {@inheritdoc}
     */
    function onError(ConnectionInterface $conn, \Exception $e) {
        return $this->_component->onError($conn, $e);
    }
}