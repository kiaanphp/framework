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
use Kiaan\Trans as Translation;

/*
|---------------------------------------------------
| Class
|---------------------------------------------------
*/
class Trans {

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
            'create : Create validator file.'
        ]);
    }

    /**
     * Create
     * 
    **/
    public function create($name, $lang)
    {
        //File
        $file = __DIR__ . '/Generation/Trans.txt';

        // Content
        $content = file_get_contents($file);

        // File name
        $file_name = Translation::getPath() . "/$lang/$name.php";

        file_put_contents($file_name, $content);

        // Success
        Cli::success(("Done, create '$name' configuration file."));
    }

}