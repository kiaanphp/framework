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
namespace Kiaan\Application\Resources\CLICommands;

/*
|---------------------------------------------------
| Uses
|---------------------------------------------------
*/
use Kiaan\Cli;
use Kiaan\App;
use Kiaan\Socket as WebSocket;

/*
|---------------------------------------------------
| Class
|---------------------------------------------------
*/
class Socket {
    
    /**
     * Handle
     * 
    **/
    public function handle()
    {
        return [
            "menu" => "menu_handle",
            "run" => "run"
        ];
    }

    /**
     * Menu handle
     * 
    **/
    public function menu_handle()
    {
        Cli::menu([
            'run : Run web socket server.'
        ]);
    }
    
    /**
     * Run
     * 
    **/
    public function run($port=8080)
    {
        $namespace = App::config()->socket->namespace;
        $namespace = str_replace(['/', '//', '.'], '\\', $namespace);

        new WebSocket($namespace, $port);

        WebSocket::run();
    }

}