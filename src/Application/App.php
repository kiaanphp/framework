<?php

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
namespace Kiaan\Application;

/*
|---------------------------------------------------
| Uses
|---------------------------------------------------
*/
use Kiaan\Application\Services;

/*
|---------------------------------------------------
| App
|---------------------------------------------------
*/
class App extends Application implements Interfaces\Framework {

    /**
    * Framework name
    **/
    protected static $framework_name = 'Kiaan';

    /**
    * Framework version
    **/
    protected static $framework_version = '1.0';

    /**
    * Settings file name
    **/
    protected static $settings_file_name = 'settings.php';

    /*
    * Run
    *
    */
    public static function run()
    {
        /**
        * Start session
        **/
        Services::session();

        /**
        * Launch framework settings
        **/
        Services::launchFrameworkSettings();

        /**
        * Env
        **/
        Services::env();

        /**
        * Run
        **/
        Services::run();
        
        /**
        * Cors enable
        **/
        Services::cors();

        /**
        * Debugger
        **/
        Services::debugger();

        /**
        * Database
        **/
        Services::database();

        /**
        * Configuration
        **/
        Services::config();

        /**
        * Url
        **/
        Services::Url();

        /**
        * Input
        **/
        Services::input();

        /**
        * Translation
        **/
        Services::trans();
        
        /**
        * Mail
        **/
        Services::mail();
        
        /**
        * Cross-site request forgery 
        **/
        Services::csrf();

        /**
        * View
        **/
        Services::view();

        /**
        * Controller
        **/
        Services::controller();

        /**
        * Middleware
        **/
        Services::middleware();

        /**
        * Validator
        **/
        Services::validator();
        
        /**
        * File
        **/
        Services::file();

        /**
        * Folder
        **/
        Services::folder();

        /**
        * Img
        **/
        Services::img();

        /**
        * View
        **/
        Services::view();

        /**
        * Plugins
        **/
        Services::plugins();

        /**
        * Response
        **/
        Services::response();

        /**
        * Event
        **/
        Services::event();

        /**
        * Notifi
        **/
        Services::notifi();
        
        /**
        * Crawl
        **/
        Services::crawl();

        /**
        * Helpers
        **/
        Services::helpers();

        /**
        * Launch script
        **/
        Services::launchScript();

        /**
        * Route
        **/
        Services::route();

        /**
        * Cli
        **/
        Services::commandLineInterface();
    }
    
    /**
	 * Helpers
     * 
	*/
    public function helpers()
    {
        return $this->prepare_path(($this->config())->helpers->path).'.php';
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
	 * Prepare path
     * 
	*/
    protected function prepare_path($path)
    {
        return str_replace(['/', '//', '.'], '\\', $path);
    }
    
    /*
    * Services
    *
    */
    public function services()
    { 
        // Framework name
        $framework_name = self::$framework_name;

        // Framework name
        $framework_version = self::$framework_version;
        
        /**
        * Settings file name
        **/
        $settings_file_name = self::$settings_file_name;

        // Script file name
        $script_filename = str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME']);
        $script_filename = explode("/", $script_filename);
        $script_filename = $script_filename[count($script_filename)-1];

        // Script Folder
        $script_folder = trim(preg_replace('/' . $script_filename . '/', '/', $_SERVER['SCRIPT_FILENAME'], 1), '/');
        
        return (object) [
            "framework_name" => $framework_name,
            "framework_version" => $framework_version,
            "script_filename" => $script_filename,
            "script_folder" => $script_folder,
            "settings_file_name" => $settings_file_name
        ];
    }
    
    /*
    * Framework
    *
    */
    public function framework()
    {    
        // Framework name
        $framework_name = self::$framework_name;

        // Framework name
        $framework_version = self::$framework_version;

        // Script file name
        $script_filename = explode("/", $_SERVER['SCRIPT_FILENAME']);
        $script_filename = $script_filename[count($script_filename)-1];

        // Script Folder
        $script_folder = trim(preg_replace('/' . $script_filename . '/', '/', $_SERVER['SCRIPT_FILENAME'], 1), '/');
        
        // Script Folder Name
        $script_folder_name = explode("/", $script_folder);
        $script_folder_name = $script_folder_name[count($script_folder_name)-1];

        // Script Folder Path
        $script_folder_path = $script_folder;

        return (object) [
            "framework" => (object) [
                "name" => $framework_name,
                "version" => $framework_version,
            ],
            "folder" => (object) [
                "name" => $script_folder_name,
                "path" => $script_folder_path,
            ],
        ];
    }

    /*
    * Settings
    *
    * Get Settings
    */
    public function settings()
    { 
        $config =  (object) include(self::$settings_file_name);
        $result = json_decode(json_encode($config), false);
        return is_object($result) ? $result : null;
    }

    /*
    * Config
    *
    * Get Configurations
    */
    public function config()
    { 
        $path = $this->prepare_path(($this->settings())->configuration->path).'.php';

        $data = include($path);
        $data = json_decode(json_encode($data));

        return $data;
    }

}