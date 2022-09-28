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
use Kiaan\Schema;

/*
|---------------------------------------------------
| Class
|---------------------------------------------------
*/
class Migration {
    
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
            "all" => "all",
            "rollback" => "rollback",
            "fresh" => "fresh"
        ];
    }

    /**
     * Menu handle
     * 
    **/
    public function menu_handle()
    {
        Cli::menu([
            'create : Create migration file.',
            'run : Run migration file.',
            'all : Migration all.',
            'rollback : Run rollback for file.',
            'fresh : Run rollback for all files.'
        ]);
    }
    
    /**
     * Create
     * 
    **/
    public function create($name)
    {
        //File
        $file = __DIR__ . '/Generation/Migrate.txt';

        // Replace variable
        $content = str_replace(
            array(":namespace:", ":class:"),
            array(Schema::getMigration()->namespace, $name),
            file_get_contents($file)
        );
        // File name
        $file_name = Schema::getMigration()->path . "/$name.php";

        file_put_contents($file_name, $content);

        // Success
        Cli::success(("Done, create '$name' migration file."));
    }

    /**
     * Run
     * 
    **/
    public function run($class)
    {
        // Run
        Schema::runMigrate($class);

        // Success
        Cli::success(("Done, '$class' is migrated."));
    }

    /**
     * All
     * 
    **/
    public function all()
    {
        // Run
        Schema::runMigrate();

        // Success
        Cli::success(("Done, all migrated."));
    }

    /**
     * Rollback
     * 
    **/
    public function rollback($class)
    {
        // Run
        Schema::runRollback($class);

        // Success
        Cli::success(("Done, '$class' is rollback."));
    }

    /**
     * Fresh
     * 
    **/
    public function fresh()
    {
        // Run
        Schema::runRollback();

        // Success
        Cli::success(("Done, all rollback."));
    }

}