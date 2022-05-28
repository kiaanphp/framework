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
use Kiaan\Plugin as myPlugin;

/*
|---------------------------------------------------
| Class
|---------------------------------------------------
*/
class Plugin {

    /**
     * Handle
     * 
    **/
    public function handle()
    {
        return [
            "menu" => "menu_handle",
            "run" => "run",
            "discovery" => "discovery",
            "update" => "update",
            "create" => "create",
            "command" => "command"
        ];
    }

    /**
     * Menu handle
     * 
    **/
    public function menu_handle()
    {
        Cli::menu([
            'run : Run plugins',
            'update : Update plugins',
            'discovery : Run auto discovery',
            'create : Create new plugin.',
            'submit : Submit plugin.'
        ]);
    }
    
    /**
     * Get root of app
     *
     * @param string $path
     * @return string $path
     */
    protected function root($path='') {
        if (PHP_SAPI != 'cli'){
            $script_filename = str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME']);
            $script_filename = explode("/", $script_filename);
            $script_filename = $script_filename[count($script_filename)-1];

            $script_folder = trim(preg_replace('/' . $script_filename . '/', '/', $_SERVER['SCRIPT_FILENAME'], 1), '/');

            $path = $script_folder . DIRECTORY_SEPARATOR . trim($path, '/');
            $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
            return $path;
        }else{
            $path = getcwd() . DIRECTORY_SEPARATOR . trim($path, '/');
            $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
            return $path;
        }
    }

    /**
     * Run
     * 
    **/
    public function run()
    {
        shell_exec("composer dumpautoload");
        myPlugin::discovery();

        Cli::success("Done.");
    }

    /**
     * Update
     * 
    **/
    public function update()
    {
        shell_exec("composer update");
        myPlugin::discovery();

        Cli::success("Done.");
    }

    /**
     * Update
     * 
    **/
    public function discovery()
    {
        myPlugin::discovery();

        Cli::success("Done.");
    }

    
    /**
     * Command
     * 
    **/
    public function command($className, $func)
    {
        // Plugin
        return myPlugin::command($className, $func);
    }

    /**
     * Create
     * 
    **/
    public function create($vendor, $className)
    {
        $myVendor = $vendor;
        $myClassName = $className;
        $vendor = strtolower($vendor);
        $className = strtolower($className);

        $dir = $this->root("vendor\\$vendor\\$className");

        # README.md
        $content = '## About

Plugin for Kiaan framework.
';

        // Create file
        if ( !file_exists($dir) ) {mkdir($dir, 0744);} chmod($dir, 755);
        file_put_contents($dir."/README.md", $content);

        # composer.json
        $content = '{
    "name": "'.$vendor.'/'.$className.'",
    "description": "Kiaan framework",
    "type": "project",
    "license": "MIT",
    "authors": [
        {
            "name": "Hassan kerdash",
            "email": "kerdashhassan@gmail.com"
        }
    ],
    "minimum-stability": "stable",
    "require": {
        "php" : "^7",
        "ext-mbstring":"*",
        "ext-json": "*"
    },
    "suggest": {
        "ext-intl": "Intl extension is useful for validating formatted numbers"
    },
    "autoload" : {
        "psr-4" : {
            "'.$myVendor.'\\\\'.$myClassName.'\\\\" : "src/"
        },
        "files" : [
            "src/helpers.php"
        ]
    },
    "extra": {
            "framework-services-enable": true,
            "framework-services-class": "'.$myVendor.'.'.$myClassName.'.Services",
            "framework-services-method": "__Services"
    }
}
';

        // Create file
        if ( !file_exists($dir) ) {mkdir($dir, 0744);} chmod($dir, 755);
        file_put_contents($dir."/composer.json", $content);

        # Create src folder
        if (!is_dir($dir.'\\src')){
            mkdir($dir.'\\src', "0777", true);
        };

# src/Services.php
$content = '<?php

/**
 * Kiaan framework
 *
 * @author Hassan Kerdash kerdashhassan@gmail.com
**/

/*
|---------------------------------------------------
| Namespaces
|---------------------------------------------------
*/
namespace '.$myVendor.'\\'.$myClassName.';

/*
|---------------------------------------------------
| Services
|---------------------------------------------------
*/
class Services {
    
    /*
    * __Services
    */
    public function __Services() {
        return true;
    }

    /*
    * Handle
    */
    public function handle() {
        return [
            "ver" => "version"
        ];
    }

    /*
    * Version
    */
    public function version(){
        echo "1.0";
    }

}
';

        // Create file
        if ( !file_exists($dir) ) {mkdir($dir, 0744);} chmod($dir, 755);
        file_put_contents($dir."/src/Services.php", $content);

# src/Helpers.php
$content = '<?php

/**
 * Kiaan framework
 *
 * @author Hassan Kerdash kerdashhassan@gmail.com
**/

/*
|---------------------------------------------------
| Helpers
|---------------------------------------------------
*/';

        // Create file
        if ( !file_exists($dir) ) {mkdir($dir, 0744);} chmod($dir, 755);
        file_put_contents($dir."/src/Helpers.php", $content);

# src/$myClassName.php
$content = '<?php

/**
 * Kiaan framework
 *
 * @author Hassan Kerdash kerdashhassan@gmail.com
**/

/*
|---------------------------------------------------
| Namespaces
|---------------------------------------------------
*/
namespace '.$myVendor.'\\'.$myClassName.';

/*
|---------------------------------------------------
| '.$myClassName.'
|---------------------------------------------------
*/
use Kiaan\Facade;
use '.$myVendor.'\\'.$myClassName.'\\Plugin\\'.$myClassName.' as Base;

/*
|---------------------------------------------------
| '.$myClassName.'
|---------------------------------------------------
*/
class '.$myClassName.' {
    
    /*
    * Facade
    *
    */
    use Facade;

    protected function __facade() {
        return Base::class;
    }

}
';

        if ( !file_exists($dir) ) {mkdir($dir, 0744);} chmod($dir, 755);
        file_put_contents($dir."/src/$myClassName.php", $content);

        # Create src/plugin folder
        if (!is_dir($dir.'\\src\\plugin')){
            mkdir($dir.'\\src\\plugin', "0777", true);
        };

# src/plugin/$myClassName.php
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
namespace '.$myVendor.'\\'.$myClassName.'\\Plugin'.';

/*
|---------------------------------------------------
| '.$myClassName.'
|---------------------------------------------------
*/
class '.$myClassName.' {

}
';

        if ( !file_exists($dir) ) {mkdir($dir, 0744);} chmod($dir, 755);
        file_put_contents($dir."/src/plugin/$myClassName.php", $content);

        // Done
        Cli::success("Done.");
    }

}