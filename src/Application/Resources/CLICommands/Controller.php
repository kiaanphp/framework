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
use Kiaan\Url;

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
    public function create($name)
    {

        $namespace = App::config()->router->namespace->controller;
        $namespace = str_replace(['/', '//', '.'], '\\', $namespace);

        $dir = App::config()->controller->path;
        $dir = str_replace(['/', '//', '.'], '\\', $dir);
        $dir = Url::root($dir);
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
use Kiaan\Dev\Controller\Build\ControllerBuild;

/*
|---------------------------------------------------
| Controller
|---------------------------------------------------
*/
class '.$name.' implements ControllerBuild {

    /*
    * Index
    */
    public function index() {
        echo "Hello World!";
    }

}';

        // Create file
        if ( !file_exists($dir) ) {mkdir($dir, 0744);}
        chmod($dir, 755);
        file_put_contents($dir."/$name.php", $content);

        // Success
        Cli::success(("Done, create '$name' controller file."));
    }

}