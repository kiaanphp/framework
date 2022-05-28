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
use Kiaan\Contact\Socket\Addons\Psr7\Functions as gPsr;
use Kiaan\Contact\Socket\Addons\Psr7\Response;

/*
|---------------------------------------------------
| Class
|---------------------------------------------------
*/
trait CloseResponseTrait {
    /**
     * Close a connection with an HTTP response
     * @param \Socket\ConnectionInterface $conn
     * @param int                          $code HTTP status code
     * @return null
     */
    private function close(ConnectionInterface $conn, $code = 400, array $additional_headers = []) {
        $response = new Response($code, array_merge([
            'X-Powered-By' => "Socket"
        ], $additional_headers));

        $conn->send(gPsr::str($response));
        $conn->close();
    }
}