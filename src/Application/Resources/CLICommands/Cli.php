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
use Kiaan\Cli as CommandLineinterface;

/*
|---------------------------------------------------
| Class
|---------------------------------------------------
*/
class Cli {

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
        CommandLineinterface::menu([
            'create : Create configuration file.'
        ]);
    }

    /**
     * Create
     * 
    **/
    public function create($name)
    {
        // File
        $file = __DIR__ . '/Generation/Cli.txt';

        // Replace variable
        $content = str_replace(
            array(":namespace:", ":class:", ":method-name:", ":method:"),
            array(CommandLineinterface::getNamespace(), $name, ucfirst(CommandLineinterface::getMethod()), CommandLineinterface::getMethod()),
            file_get_contents($file)
        );
        // File name
        $file_name = CommandLineinterface::getPath(). "/$name.php";

        file_put_contents($file_name, $content);

        // Success
        CommandLineinterface::success(("Done, create '$name' Command line interface file."));
    }

}