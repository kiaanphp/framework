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
use Kiaan\Contact\Socket\RFC6455\Messaging\MessageBuffer;

/*
|---------------------------------------------------
| Class
|---------------------------------------------------
*/
class ConnContext {
    /**
     * @var \Socket\WebSocket\WsConnection
     */
    public $connection;

    /**
     * @var \Socket\RFC6455\Messaging\MessageBuffer;
     */
    public $buffer;

    public function __construct(WsConnection $conn, MessageBuffer $buffer) {
        $this->connection = $conn;
        $this->buffer = $buffer;
    }
}
