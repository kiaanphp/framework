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

/*
|---------------------------------------------------
| Class
|---------------------------------------------------
*/
class Server {
    
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
            'run : Run server.'
        ]);
    }
    
    /**
     * Run
     * 
    **/
    public function run($host="127.0.0.1", $port="8000")
    {
        shell_exec("php -S $host:$port");
    }

}