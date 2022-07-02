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
use Kiaan\App;
use Kiaan\Debugger;
use Kiaan\Mail;
use Kiaan\Env;
use Kiaan\Config;
use Kiaan\Trans;
use Kiaan\Csrf;
use Kiaan\Validator;
use Kiaan\Cli;
use Kiaan\Route;
use Kiaan\Controller;
use Kiaan\Middleware;
use Kiaan\DB;
use Kiaan\Schema;
use Kiaan\Auth;
use Kiaan\pdoDB;
use Kiaan\Url;
use Kiaan\File;
use Kiaan\Folder;
use Kiaan\Img;
use Kiaan\Plugin;
use Kiaan\Input;
use Kiaan\View;

/*
|---------------------------------------------------
| Services
|---------------------------------------------------
*/
Class Services implements Interfaces\Framework {

    /**
    * Settings
    **/
    protected static $settings;

    /**
    * Configuration
    **/
    protected static $config;
    
    /*
    * Singleton pattern
    */
    protected function __construct() {}

    /**
    * Run
    **/
    public static function run() {
        // Turn on output buffering    
        ob_start();

        // Information pass to $_SERVER
        self::toServer();
        
        // Root of app
        self::root();

        // launch settings
        self::launchSettings();
    }
    
    /**
    * Information pass to $_SERVER
    **/
    public static function toServer() {
        // Services
        $services = App::services();

        // Framework name
        $_SERVER['FRAMEWORK_NAME'] = $services->framework_name;

        // Framework name
        $_SERVER['FRAMEWORK_VERSION'] = $services->framework_version;

        // Script file name
        $_SERVER['SCRIPT_FILE_NAME'] = $services->script_filename;

        // Script Folder
        $_SERVER['SCRIPT_FOLDER'] = $services->script_folder;
     }

    /**
    * launch framework settings
    **/
    public static function launchFrameworkSettings() {
        $path = self::root(App::services()->settings_file_name);

        $data = include($path);
        $data = json_decode(json_encode($data)); 

        return self::$settings = $data;
     }

    /**
    * launch settings
    **/
    public static function launchSettings() {
        $path = self::$settings->configuration->path.'.php';

        $data = include($path);
        $data = json_decode(json_encode($data));
        
        return self::$config = $data;
     }

    /**
     * Get root of app
     *
     * @param string $path
     * @return string $path
     */
    protected static function root($path='') {
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
     * Get url
     *
     * @param string $path
     * @return string $path
    */
    protected static function getUrl($path='') {
        $protocol = ($_SERVER['REQUEST_SCHEME'] ?? 'http') . '://';
        $host = $_SERVER['HTTP_HOST'] ?? null;
        
        $script_name = str_replace('\\', '', dirname($_SERVER['SCRIPT_NAME']));
        $script_name = str_replace('/public', '', $script_name).'/'.$path;

        return $base_url = $protocol . $host . $script_name;
    }

    /**
    * Url
    **/
    public static function url() {
        Url::setPublicPath(self::$config->fileSystem->public);
    }

    /**
    * File
    **/
    public static function file() {
        new File();
        File::setRootPath(self::root());
        File::fileSystem("path", true);
    }

    /**
    * Folder
    **/
    public static function folder() {
        new Folder();
        Folder::setRootPath(self::root());
        Folder::fileSystem("path", true);
    }

    /**
    * Image
    **/
    public static function img() {
        new Img();
        Img::setRootPath(self::root());
        Img::fileSystem("path", true);
    }

    /**
    * Session start
    **/
    public static function session() {
        if (!session_id()) {
            ini_set('session.use_only_cookies', 1);
            session_start();
        }
    }

    /**
    * Config
    **/
    public static function Config() {
        new Config();
        Config::setRootPath(self::root());
        Config::setFolderPath(self::root(self::$config->configuration->path));
        Config::fileSystem("folder", true);

        // String
        $string = [
            'driver' => self::$config->db->driver,
            'host' => self::$config->db->host,
            'db' => self::$config->db->db,
            'user' => self::$config->db->user,
            'pass' => self::$config->db->pass,
            'port' => self::$config->db->port,
        ];

        // Database
        new pdoDB();
        pdoDB::connect(
            $string, ( self::$config->db->error === true || trim(self::$config->db->error == 'true')) ? true : false
        );
        $pdo = pdoDB::connection();

        Config::setPdo($pdo);
        Config::setTable(self::$config->configuration->database->table);
        $config = Config::db();

        if(self::$config->db->error === true || trim(self::$config->db->error == 'true')){
            $config = $config->migration();
        }
    }

    /**
    * Trans
    **/
    public static function Trans() {
        new Trans(self::$config->languages->defaultLang);
        Trans::setRootPath(self::root());
        Trans::setFolderPath(self::root(self::$config->languages->path));
        Trans::fileSystem("folder", true);
    }
    
    /**
    * View
    **/
    public static function view() {
        // Views folder
        new View (self::$config->views->path);  

        // Roots path
        View::setRootsPath(
            self::root(""),
            self::$config->fileSystem->public
        );

        // Method
        $method = self::$config->security->method->input;
        $method = (empty($method)) ? '_method' : $method ;
        View::setMethod($method);

        // CSRF
        View::setCsrf(
            self::$config->security->csrf->input ?? '_csrf',
            Csrf::get()
        );
    }
    
    public static function env() {
        // Env
        new Env();

        // FileSystem
        Env::setRootPath(self::root());
        Env::fileSystem("path", true);

        // Load
        Env::load()
        ->file(self::$settings->env->file)
        ->submit();
    }

    /**
    * Database
    **/
    public static function database() {
        // String
        $string = [
            'driver' => self::$config->db->driver,
            'host' => self::$config->db->host,
            'db' => self::$config->db->db,
            'user' => self::$config->db->user,
            'pass' => self::$config->db->pass,
            'port' => self::$config->db->port,
        ];

        // Pdo
        new pdoDB();
        pdoDB::connect(
            $string, ( self::$config->db->error === true || trim(self::$config->db->error == 'true')) ? true : false
        );
        $pdo = pdoDB::connection();

        // Connect DB
        new DB($pdo);

        DB::setPrimaryKey(self::$config->db->models->primaryKey);

        // Connect schema
        new Schema($pdo);

        // Connect auth
        new Auth($pdo);
        Auth::setTable(self::$config->db->auth->table);
        Auth::setLoginSession(self::$config->db->auth->loginSession);
        Auth::setFields(
            self::$config->db->auth->fields->primaryField,
            self::$config->db->auth->fields->idField,
            self::$config->db->auth->fields->passField
        );
    }

    /**
    * Mail
    **/
    public static function mail() {
        new Mail(
            self::$config->mail->from->email,
            self::$config->mail->from->name,
            self::$config->mail->host,
            self::$config->mail->port,
            self::$config->mail->protocol,
            self::$config->mail->username,
            self::$config->mail->password,
        );
    }

    /**
    * launch script
    **/
    public static function launchScript() {
        $services_script_namespace = self::$settings->services->namespace;
        $services_script_method = self::$settings->services->method;

        $handle = new $services_script_namespace;
        $handle->{$services_script_method}();
    }

    /**
    * Debugger
    **/
    public static function debugger() {
        // Pages
        $pages = [
            '403' => (empty(self::$config->errors->{403}->page)) ? 'Debugger\\resources\\403.php' : self::root(self::$config->errors->{403}->page),
            '404' => (empty(self::$config->errors->{404}->page)) ? 'Debugger\\resources\\404.php' : self::root(self::$config->errors->{404}->page),
            '500' => (empty(self::$config->errors->{500}->page)) ? 'Debugger\\resources\\500.php' : self::root(self::$config->errors->{500}->page),
        ];
        Debugger::setPages($pages);

        // Run
        if (self::$config->security->debugger->enable) {
            Debugger::run();
        }
    } 

    /**
    * Cross-site request forgery 
    **/
    public static function csrf() {
        Csrf::setKey(self::$config->security->csrf->key ?? '_csrf');
        Csrf::setInput(self::$config->security->csrf->input ?? '_csrf');

        // Csrf
        Csrf::run();
    }

    /**
    * Validator
    **/
    public static function validator() {
        // Rules namespace
        Validator::setNamespace(self::$config->validator->namespace);
        Validator::setDefaultMethod(self::$config->validator->defaultMethod);

        // List of rules
        $list = [
            'archive' => "Kiaan\\Security\\Validator\\Resources\\Rules\\archiveRule",
            'array' => "Kiaan\\Security\\Validator\\Resources\\Rules\\arrayRule",
            'audio' => "Kiaan\\Security\\Validator\\Resources\\Rules\\audioRule",
            'bool' => "Kiaan\\Security\\Validator\\Resources\\Rules\\boolRule",
            'date' => "Kiaan\\Security\\Validator\\Resources\\Rules\\dateRule",
            'email' => "Kiaan\\Security\\Validator\\Resources\\Rules\\emailRule",
            'empty' => "Kiaan\\Security\\Validator\\Resources\\Rules\\emptyRule",
            'extension' => "Kiaan\\Security\\Validator\\Resources\\Rules\\extensionRule",
            'file' => "Kiaan\\Security\\Validator\\Resources\\Rules\\fileRule",
            'filled' => "Kiaan\\Security\\Validator\\Resources\\Rules\\filledRule",
            'float' => "Kiaan\\Security\\Validator\\Resources\\Rules\\floatRule",
            'image' => "Kiaan\\Security\\Validator\\Resources\\Rules\\imageRule",
            'integer' => "Kiaan\\Security\\Validator\\Resources\\Rules\\integerRule",
            'ip' => "Kiaan\\Security\\Validator\\Resources\\Rules\\ipRule",
            'ipv4' => "Kiaan\\Security\\Validator\\Resources\\Rules\\ipv4Rule",
            'ipv6' => "Kiaan\\Security\\Validator\\Resources\\Rules\\ipv6Rule",
            'json' => "Kiaan\\Security\\Validator\\Resources\\Rules\\jsonRule",
            'max' => "Kiaan\\Security\\Validator\\Resources\\Rules\\maxRule",
            'mime' => "Kiaan\\Security\\Validator\\Resources\\Rules\\mimeRule",
            'min' => "Kiaan\\Security\\Validator\\Resources\\Rules\\minRule",
            'null' => "Kiaan\\Security\\Validator\\Resources\\Rules\\nullRule",
            'numeric' => "Kiaan\\Security\\Validator\\Resources\\Rules\\numericRule",
            'object' => "Kiaan\\Security\\Validator\\Resources\\Rules\\objectRule",
            'required' => "Kiaan\\Security\\Validator\\Resources\\Rules\\requiredRule",
            'size' => "Kiaan\\Security\\Validator\\Resources\\Rules\\sizeRule",
            'string' => "Kiaan\\Security\\Validator\\Resources\\Rules\\stringRule",
            'url' => "Kiaan\\Security\\Validator\\Resources\\Rules\\urlRule",
            'video' => "Kiaan\\Security\\Validator\\Resources\\Rules\\videoRule",
            'same' => "Kiaan\\Security\\Validator\\Resources\\Rules\\sameRule"
        ];

        // Validator rules
        Validator::addRules($list);
    }

    /**
    * Cli
    **/
    public static function commandLineInterface() {
            // Commands namespace
            Cli::setNamespace(self::$config->cli->namespace);
            Cli::setDefaultMethod(self::$config->cli->defaultMethod);

            // List of commands
            $list = [
                "server" => "Kiaan\\Application\\Resources\\CLICommands\\Server,handle",
                "model" => "Kiaan\\Application\\Resources\\CLICommands\\Model,handle",
                "migrate" => "Kiaan\\Application\\Resources\\CLICommands\\Migrate,handle",
                "seed" => "Kiaan\\Application\\Resources\\CLICommands\\Seed,handle",
                "socket" => "Kiaan\\Application\\Resources\\CLICommands\\socket,handle",
                "config" => "Kiaan\\Application\\Resources\\CLICommands\\Config,handle",
                "trans" => "Kiaan\\Application\\Resources\\CLICommands\\Trans,handle",
                "cli" => "Kiaan\\Application\\Resources\\CLICommands\\Cli,handle",
                "controller" => "Kiaan\\Application\\Resources\\CLICommands\\Controller,handle",
                "middleware" => "Kiaan\\Application\\Resources\\CLICommands\\Middleware,handle",
                "validator" => "Kiaan\\Application\\Resources\\CLICommands\\Validato,handler",
                "plugin" => "Kiaan\\Application\\Resources\\CLICommands\\Plugin,handle"
            ];

            // Cli commands
            Cli::addCommands($list);

            if(PHP_SAPI == 'cli'){
                // Handle
                if(sizeof($_SERVER['argv']) > 1){
                    Cli::handle();
                }else{
                    Cli::warn(("    Welcome to Kiaan framework!   "), 'red');
                    Cli::notice(("  Easy, flexible and professional!   "), 'yellow');
                }
            }
    }

    /**
    * Plugins
    **/
    public static function plugins() {
        Plugin::setHandle("handle");
        Plugin::setExtra([
            "servicesEnable" => "framework-services-enable",
            "services" => "framework-services-class",
            "servicesMethod" => "framework-services-method",
        ]);

        // Plugin services run
        Plugin::servicesRun();
    }

    /**
    * Input
    **/
    public static function input() {
        new Input();
        Input::setRootPath(self::root());
        Input::fileSystem("path", true);
    }

    /**
    * Controller
    **/
    public static function controller() {
        new Controller(
            self::$config->controller->namespace,
            self::$config->controller->defaultMethod
        );
    }

    /**
    * Middleware
    **/
    public static function middleware() {
        new Middleware(
            self::$config->middleware->namespace,
            self::$config->middleware->defaultMethod
        );
    }

    /**
    * Route
    **/
    public static function route() {
        // Eanble
        if (self::$config->router->enable) {

            // Prefix API 
            Route::setPrefixGates("api", self::$config->router->api->prefix);

            // Controller namespace
            Route::setControllerNamespace(Controller::getNamespace());

            // Controller default method
            Route::setControllerDefaultMethod(Controller::getDefaultMethod());

            // Middleware namespace
            Route::setMiddlewareNamespace(Middleware::getNamespace());

            // Middleware default method
            Route::setMiddlewareDefaultMethod(Middleware::getDefaultMethod());

            // Cross-site request forgery
            Route::setCsrf([
                "enable" => self::$config->security->csrf->enable,
                "value" => Csrf::get(),
                "input"=> self::$config->security->csrf->input ?? '_csrf'
            ]);

            // Method input
            $method = self::$config->security->method->input;
            $method = (empty($method)) ? '_method' : $method ;
            Route::setMethodInput($method);

            // Web routes
            array_map(function($file){
                $prefix_path = self::$config->router->path;
                $file_path = $prefix_path . DIRECTORY_SEPARATOR . $file . '.php';
                $file_path = self::root($file_path);
                
                if(!is_file($file_path)){
                    $file_path = str_replace(['/', '//', '.'], '\\', $file);
                    $file_path = $file_path . '.php';
                    $file_path = self::root($file_path);
                }

                include_once($file_path);
            }, self::$config->router->routes->web);

            // API routes
            Route::api(function(){
                array_map(function($file){
                    $prefix_path = self::$config->router->path;
                    $file_path = $prefix_path . DIRECTORY_SEPARATOR . $file . '.php';
                    $file_path = self::root($file_path);
    
                    if(!is_file($file_path)){
                        $file_path = str_replace(['/', '//', '.'], '\\', $file);
                        $file_path = $file_path . '.php';
                        $file_path = self::root($file_path);
                    }
    
                    include_once($file_path);
                }, self::$config->router->routes->api);
            });

            // Run && handle
            Route::run();
        }
    }
    
}