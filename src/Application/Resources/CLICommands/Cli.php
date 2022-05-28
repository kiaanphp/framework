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
use Kiaan\App;
use Kiaan\Url;

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
        // Namespace
        $namespace = App::config()->cli->namespace;
        $namespace = str_replace(['/', '//', '.'], '\\', $namespace);

        // Dir
        $dir = App::config()->cli->path;
        $dir = str_replace(['/', '//', '.'], '\\', $dir);
        $dir = Url::root($dir);

        // Content
        $content = '<?php

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
namespace '.$namespace.';

/*
|---------------------------------------------------
| Uses
|---------------------------------------------------
*/
use Kiaan\Dev\Cli\Build\CliBuild;
use Kiaan\Cli;

/*
|---------------------------------------------------
| Cli
|---------------------------------------------------
*/
class '.$name.' implements CliBuild {

    /**
     * Handle
     * 
    **/
    public function handle()
    {
        return [
            "menu" => "menu_handle",
            "test" => "test"
        ];
    }  

    /**
     * Menu handle
     * 
    **/
    public function menu_handle()
    {
        Cli::menu(["test"]);
    }

    /**
     * Test
     * 
    **/
    public function test()
    {
        echo "Hello_World";
    }

}';

        // Create file
        if ( !file_exists($dir) ) {mkdir($dir, 0744);}
        chmod($dir, 755);
        file_put_contents($dir."/$name.php", $content);

        // Success
        CommandLineinterface::success(("Done, create '$name' CLI file."));
    }

}