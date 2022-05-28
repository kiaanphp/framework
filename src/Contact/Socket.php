<?php

/**
 * Kiaan framework
 *
 * @author Hassan Kerdash kerdashhassan@gmail.com
 **/

/*
|---------------------------------------------------
| Namespace
|---------------------------------------------------
*/
namespace Kiaan\Contact;

/*
|---------------------------------------------------
| Use
|---------------------------------------------------
*/
use Kiaan\Contact\Socket\Server\IoServer;
use Kiaan\Contact\Socket\Http\HttpServer;
use Kiaan\Contact\Socket\WebSocket\WsServer;

/*
|---------------------------------------------------
| Mail
|---------------------------------------------------
*/
class Socket {

    /*
    * Socket
    */
    public $socket;

    /*
    * Construct
    * 
    */
    public function __construct($class, $port=8080) {
        $this->start($class, $port=8080);
    }

    /*
    * Start
    * 
    */
    public function start($class, $port=8080) {
        // Class
        if(!class_exists($class)) {
            $class = str_replace(['/', '//', '.'], '\\', $class);
        }

        // Socket
        $socket = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new $class
                )
            ),
            $port
        );

        $this->socket = $socket;
    }

    /*
    * Run
    * 
    */
    public function run() {
        $this->socket->run();
    }


}