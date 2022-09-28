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
use Kiaan\Validator as Validation;

/*
|---------------------------------------------------
| Class
|---------------------------------------------------
*/
class Validator {

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
    public function create($name)
    {
        //File
        $file = __DIR__ . '/Generation/Validator.txt';

        // Replace variable
        $content = str_replace(
            array(":namespace:", ":class:"),
            array(Validation::getNamespace(), $name),
            file_get_contents($file)
        );

        // File name
        $file_name = Validation::getPath() . "/$name.php";

        file_put_contents($file_name, $content);

        // Success
        Cli::success(("Done, create '$name' validator file."));
    }

}