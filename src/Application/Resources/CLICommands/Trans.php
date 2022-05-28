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
class Trans {

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
    public function create($lang, $name)
    {
        $dir = App::config()->languages->path;
        $dir = Url::root($dir);
        $content = '<?php

/**
 * Kiaan framework
 *
 * @author Hassan Kerdash kerdashhassan@gmail.com
**/

/*
|---------------------------------------------------
| Translation
|---------------------------------------------------
*/
return [
    "key" => "value",
];';

        // Create folder
        if (!is_dir($dir."/$lang")){
            mkdir($dir."/$lang", "0777", true);
        };

        // Create file
        if ( !file_exists($dir) ) {mkdir($dir, 0744);}
        chmod($dir, 755);
        file_put_contents($dir."/$lang/$name.php", $content);

        // Success
        Cli::success(("Done, create '$name' configuration file."));
    }

}