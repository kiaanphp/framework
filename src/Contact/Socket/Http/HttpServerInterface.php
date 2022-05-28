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
use Kiaan\Contact\Socket\MessageComponentInterface;
use Kiaan\Contact\Socket\ConnectionInterface;
use Kiaan\Contact\Socket\Addons\Psr\RequestInterface;

/*
|---------------------------------------------------
| Class
|---------------------------------------------------
*/
interface HttpServerInterface extends MessageComponentInterface {
    /**
     * @param \Socket\ConnectionInterface          $conn
     * @param \Psr\Http\Message\RequestInterface    $request null is default because PHP won't let me overload; don't pass null!!!
     * @throws \UnexpectedValueException if a RequestInterface is not passed
     */
    public function onOpen(ConnectionInterface $conn, RequestInterface $request = null);
}
