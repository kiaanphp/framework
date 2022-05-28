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
namespace Kiaan\Contact\Socket;

/**
 * Version
 * @var string
 */
const VERSION = 'Socket';

/*
|---------------------------------------------------
| Class
|---------------------------------------------------
*/
interface ConnectionInterface {
    /**
     * Send data to the connection
     * @param  string $data
     * @return \Socket\ConnectionInterface
     */
    function send($data);

    /**
     * Close the connection
     */
    function close();
}
