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
use Kiaan\Config as ConfigClass;

/*
|---------------------------------------------------
| Class
|---------------------------------------------------
*/
class Config {

    /**
     * Handle
     * 
    **/
    public function handle()
    {
        return [
            "menu" => "menu_handle",
            "create" => "create"
        ];
    }

    /**
     * Menu handle
     * 
    **/
    public function menu_handle()
    {
        Cli::menu([
            'create : Create configuration file.'
        ]);
    }

    /**
     * Create
     * 
    **/
    public function create($name)
    {        
        //File
        $file = __DIR__ . '/Generation/Config.txt';

        // Content
        $content = file_get_contents($file);

        // File name
        $file_name = ConfigClass::getPath() . "/$name.php";

        file_put_contents($file_name, $content);

        // Success
        Cli::success(("Done, create '$name' configuration file."));
    }

}