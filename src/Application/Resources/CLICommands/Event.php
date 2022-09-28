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
use Kiaan\Event as Events;

/*
|---------------------------------------------------
| Class
|---------------------------------------------------
*/
class Event {
    
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
        Cli::menu(['create : create event class file.']);
    }
    
    /**
     * Create
     * 
    **/
    public function create($name)
    {
        //File
        $file = __DIR__ . '/Generation/Event.txt';

        // Replace variable
        $content = str_replace(
            array(":class:"),
            array($name),
            file_get_contents($file)
        );

        // File name
        $file_name = Events::getPath() . "/$name.php";

        file_put_contents($file_name, $content);

        // Success
        Cli::success(("Done, create '$name' event file."));
    }

}