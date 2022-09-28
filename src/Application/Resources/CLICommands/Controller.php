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
use Kiaan\Controller as ControllerClass;

/*
|---------------------------------------------------
| Class
|---------------------------------------------------
*/
class Controller {

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
            'create : Create controller file.'
        ]);
    }

    /**
     * Create
     * 
    **/
    public function create($name, $type='controller')
    {
        //File
        switch ($type) {
            case 'controller':
                $file = __DIR__ . '/Generation/Controller.txt';
                break;
            case 'crud':
                $file = __DIR__ . '/Generation/Controller-Crud.txt';
                break;
            case 'api':
                $file = __DIR__ . '/Generation/Controller-Api.txt';
                break;
        }

        // Replace variable
        $content = str_replace(
            array(":namespace:", ":class:", ":method-name:", ":method:"),
            array(ControllerClass::getNamespace(), $name, ucfirst(ControllerClass::getDefaultMethod()), ControllerClass::getDefaultMethod()),
            file_get_contents($file)
        );
        // File name
        $file_name = ControllerClass::getPath(). "/$name.php";

        file_put_contents($file_name, $content);

        // Success
        Cli::success(("Done, create '$name' controller file."));
    }

}