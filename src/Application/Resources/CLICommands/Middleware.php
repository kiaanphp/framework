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
use Kiaan\Middleware as MiddlewareClass;

/*
|---------------------------------------------------
| Class
|---------------------------------------------------
*/
class Middleware {

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
            'create : Create middleware file.'
        ]);
    }

    /**
     * Create
     * 
    **/
    public function create($name)
    {
        //File
        $file = __DIR__ . '/Generation/Middleware.txt';

        // Replace variable
        $content = str_replace(
            array(":namespace:", ":class:", ":method:"),
            array(MiddlewareClass::getNamespace(), $name, MiddlewareClass::getmethod()),
            file_get_contents($file)
        );
        // File name
        $file_name = MiddlewareClass::getPath(). "/$name.php";

        file_put_contents($file_name, $content);

        // Success
        Cli::success(("Done, create '$name' middleware file."));
    }

}