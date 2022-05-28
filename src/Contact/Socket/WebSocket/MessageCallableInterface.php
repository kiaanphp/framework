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
use Kiaan\Contact\Socket\ConnectionInterface;
use Kiaan\Contact\Socket\RFC6455\Messaging\MessageInterface;

/*
|---------------------------------------------------
| Class
|---------------------------------------------------
*/
interface MessageCallableInterface {
    public function onMessage(ConnectionInterface $conn, MessageInterface $msg);
}
