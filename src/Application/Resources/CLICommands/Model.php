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
use Kiaan\DB;

/*
|---------------------------------------------------
| Class
|---------------------------------------------------
*/
class Model {
    
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
        Cli::menu(['create']);
    }
    
    /**
     * Create
     * 
    **/
    public function create($name, $table='')
    {
        // Table
        $table = (empty($table)) ? $name : $table;

        //File
        $file = __DIR__ . '/Generation/Model.txt';

        // Replace variable
        $content = str_replace(
            array(":namespace:", ":class:", ":table:"),
            array(DB::getModel()->namespace, $name, $table),
            file_get_contents($file)
        );

        // File name
        $file_name = DB::getModel()->path . "/$name.php";

        file_put_contents($file_name, $content);

        // Success
        Cli::success(("Done, create '$name' model file."));
    }

}