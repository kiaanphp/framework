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
use Kiaan\Schema;

/*
|---------------------------------------------------
| Class
|---------------------------------------------------
*/
class Seed {
    
    /**
     * Handle
     * 
    **/
    public function handle()
    {
        return [
            "menu" => "menu_handle",
            "create" => "create",
            "run" => "run",
            "all" => "all"
        ];
    }

    /**
     * Menu handle
     * 
    **/
    public function menu_handle()
    {
        Cli::menu([
            'create : Create seeding file.',
            'run : Run seed file.',
            'all : Seed all.'
        ]);
    }
    
    /**
     * Create
     * 
    **/
    public function create($name)
    {
        //File
        $file = __DIR__ . '/Generation/Seed.txt';

        // Replace variable
        $content = str_replace(
            array(":namespace:", ":class:"),
            array(Schema::getSeed()->namespace, $name),
            file_get_contents($file)
        );
        // File name
        $file_name = Schema::getSeed()->path . "/$name.php";

        file_put_contents($file_name, $content);

        // Success
        Cli::success(("Done, create '$name' seeding file."));
    }

    /**
     * Run
     * 
    **/
    public function run($class)
    {
        // Run
        Schema::runSeeds($class);

        // Success
        Cli::success(("Done, '$class' is seeded."));
    }

    /**
     * All
     * 
    **/
    public function all()
    {
        // Run
        Schema::runSeeds();

        // Success
        Cli::success(("Done, all seeded."));
    }

}