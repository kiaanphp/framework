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
class Config {

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
            'create : Create configuration file.'
        ]);
    }

    /**
     * Create
     * 
    **/
    public function create($name)
    {
        $dir = App::config()->configuration->path;
        $dir = Url::root($dir);
        $content = '<?php

/**
 * Kiaan framework
 *
 * @author Hassan Kerdash kerdashhassan@gmail.com
**/

/*
|---------------------------------------------------
| Configurations
|---------------------------------------------------
*/
return [
    "key" => "value",
];';

        // Create file
        if ( !file_exists($dir) ) {mkdir($dir, 0744);}
        chmod($dir, 755);
        file_put_contents($dir."/$name.php", $content);

        // Success
        Cli::success(("Done, create '$name' configuration file."));
    }

}