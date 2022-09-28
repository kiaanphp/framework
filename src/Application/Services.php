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
use Kiaan\Response;
use Kiaan\Event;
use Kiaan\Crawl;
use Kiaan\Cors;
use Kiaan\Notifi;

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

        return $protocol . $host . $script_name;
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
        File::filesystemRoot(self::root());
    }

    /**
    * Folder
    **/
    public static function folder() {
        Folder::filesystemRoot(self::root());
    }

    /**
    * Image
    **/
    public static function img() {
        Img::filesystemRoot(self::root());
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
        // Config
        new Config();
        Config::filesystemRoot(self::root());
        Config::filesystemPath(self::root(self::$config->configuration->path));

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
        $pdo = pdoDB::connection();

        // Config
        Config::setPdo($pdo);
        Config::setTable(self::$config->configuration->database->table);
    }

    /**
    * Trans
    **/
    public static function Trans() {
        Trans::setLocal(self::$config->languages->defaultLang);
        Trans::filesystemRoot(self::root());
        Trans::filesystemPath(self::root(self::$config->languages->path));
    }
    
    /**
    * View
    **/
    public static function view() {
        // Views folder
        new View();  

        // Path
        View::setPath(self::$config->views->path);

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

        // Classes
        View::classes([
            "App" => "\Kiaan\App",
            "Auth" => "\Kiaan\Auth",
            "Classes" => "\Kiaan\Classes",
            "Cli" => "\Kiaan\Cli",
            "Collection" => "\Kiaan\Collection",
            "Config" => "\Kiaan\Config",
            "Controller" => "\Kiaan\Controller",
            "Cookie" => "\Kiaan\Cookie",
            "Cors" => "\Kiaan\Cors",
            "Crawl" => "\Kiaan\Crawl",
            "Crypt" => "\Kiaan\Crypt",
            "Csrf" => "\Kiaan\Csrf",
            "DB" => "\Kiaan\DB",
            "Debugger" => "\Kiaan\Debugger",
            "Env" => "\Kiaan\Env",
            "Facade" => "\Kiaan\Facade",
            "File" => "\Kiaan\File",
            "Folder" => "\Kiaan\Folder",
            "Func" => "\Kiaan\Func",
            "Http" => "\Kiaan\Http",
            "Img" => "\Kiaan\Img",
            "Input" => "\Kiaan\Input",
            "Ip" => "\Kiaan\Ip",
            "Mail" => "\Kiaan\Mail",
            "Math" => "\Kiaan\Math",
            "Middleware" => "\Kiaan\Middleware",
            "Model" => "\Kiaan\Model",
            "Number" => "\Kiaan\Number",
            "Obfuscate" => "\Kiaan\Obfuscate",
            "Password" => "\Kiaan\Password",
            "PdoDB" => "\Kiaan\PdoDB",
            "Plugin" => "\Kiaan\Plugin",
            "Random" => "\Kiaan\Random",
            "Request" => "\Kiaan\Request",
            "Response" => "\Kiaan\Response",
            "Route" => "\Kiaan\Route",
            "Schema" => "\Kiaan\Schema",
            "Session" => "\Kiaan\Session",
            "Str" => "\Kiaan\Str",
            "Time" => "\Kiaan\Time",
            "Trans" => "\Kiaan\Trans",
            "Url" => "\Kiaan\Url",
            "Validation" => "\Kiaan\Validation",
            "Validator" => "\Kiaan\Validator",
            "Variable" => "\Kiaan\Variable",
            "View" => "\Kiaan\View",
            "Event" => "\Kiaan\Event"
        ]);
    }
    
    public static function env() {
        // Env
        new Env();

        // FileSystem
        Env::filesystemRoot(self::root());

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
        if(self::$config->db->driver=='sqlite'){
            self::$config->db->host = self::$config->db->path.DIRECTORY_SEPARATOR.self::$config->db->db;
        }
        
        $string = [
            'driver' => self::$config->db->driver,
            'host' => self::$config->db->host,
            'db' => self::$config->db->db,
            'user' => self::$config->db->user,
            'pass' => self::$config->db->pass,
            'port' => self::$config->db->port,
        ];

        //Pdo connect
        if(self::$config->db->connect === true || trim(self::$config->db->connect == 'true')){
            // PDO
            new pdoDB();

            pdoDB::connect(
                $string, (self::$config->db->error === true || trim(self::$config->db->error == 'true')) ? true : false
            );
        }

        $pdo = pdoDB::connection();

        // Connect DB
        new DB();
        DB::setPdo($pdo);
        DB::setPrimaryKey(self::$config->db->models->primaryKey);
        DB::setModel(self::$config->db->models->namespace, self::$config->db->models->path);
        
        // Schema
        new Schema();
        Schema::setConnect($pdo);
        Schema::setMigration(
            self::$config->db->migrations->namespace,
            self::$config->db->migrations->table,
        );
        Schema::setMigrationPath(self::$config->db->migrations->path);
        Schema::setSeed(
            self::$config->db->seeds->namespace,
        );
        Schema::setSeedPath(self::$config->db->seeds->path);
            
        // Auth
        new Auth();
        Auth::setConnect($pdo);
        Auth::setTable(self::$config->db->auth->table);
        Auth::setLoginSession(self::$config->db->auth->loginSession);
        Auth::setJwtHeader(self::$config->db->auth->jwt->header, self::$config->db->auth->jwt->headerPrefix);
        Auth::setFields(
            self::$config->db->auth->fields->primaryField,
            self::$config->db->auth->fields->idField,
            self::$config->db->auth->fields->passField,
            self::$config->db->auth->fields->tokenField,
            self::$config->db->auth->fields->jwtField
        );
    }

    /**
    * Mail
    **/
    public static function mail() {
        new Mail();
        Mail::mailer(self::$config->mail->driver);
        Mail::from(self::$config->mail->from->email, self::$config->mail->from->name);
        Mail::server(self::$config->mail->host, self::$config->mail->port);
        Mail::protocol(self::$config->mail->protocol);
        Mail::login(self::$config->mail->username, self::$config->mail->password);

        Mail::filesystemRoot(self::root());
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
        // View
        self::view();

        // Debugger
        new Debugger;

        // File system root
        Debugger::filesystemRoot(self::root());
        
        // Pages
        foreach (array(404, 500) as $code) {
            if(self::$config->debug->{$code}->template && !is_null(self::$config->debug->{$code}->page)){
                Debugger::viewCode(View::html(self::$config->debug->{$code}->page), $code);
            }else{
                Debugger::view(self::$config->debug->{$code}->page, $code);
            }
        }

        // Run
        if (self::$config->debug->enable === true || trim(self::$config->debug->enable) === 'true') {
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
        // PDO Connection
        $pdo = pdoDB::connection();

        // Validator
        Validator::setPdo($pdo);
        Validator::setNamespace(self::$config->validator->namespace);
        Validator::setPath(self::$config->validator->path);
        Validator::run();
    }

    /**
    * Cli
    **/
    public static function commandLineInterface() {
            // Commands namespace
            Cli::setNamespace(self::$config->cli->namespace);

            // Path
            Cli::setPath(self::root(self::$config->cli->path));
            
            // List of commands
            $list = [
                "server" => "Kiaan\\Application\\Resources\\CLICommands\\Server",
                "model" => "Kiaan\\Application\\Resources\\CLICommands\\Model",
                "migration" => "Kiaan\\Application\\Resources\\CLICommands\\Migration",
                "seed" => "Kiaan\\Application\\Resources\\CLICommands\\Seed",
                "config" => "Kiaan\\Application\\Resources\\CLICommands\\Config",
                "trans" => "Kiaan\\Application\\Resources\\CLICommands\\Trans",
                "cli" => "Kiaan\\Application\\Resources\\CLICommands\\Cli",
                "controller" => "Kiaan\\Application\\Resources\\CLICommands\\Controller",
                "middleware" => "Kiaan\\Application\\Resources\\CLICommands\\Middleware",
                "validator" => "Kiaan\\Application\\Resources\\CLICommands\\Validator",
                "route" => "Kiaan\\Application\\Resources\\CLICommands\\Route",
                "event" => "Kiaan\\Application\\Resources\\CLICommands\\Event",
                "plugin" => "Kiaan\\Application\\Resources\\CLICommands\\Plugin"
            ];

            // Cli commands
            Cli::commands($list);

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
        Input::filesystemRoot(self::root());
    }

    /**
    * Controller
    **/
    public static function controller() {
        Controller::setNamespace(self::$config->controller->namespace);
        Controller::setPath(self::root(self::$config->controller->path));
        Controller::setDefaultMethod(self::$config->controller->defaultMethod);
    }

    /**
    * Middleware
    **/
    public static function middleware() {
        Middleware::setNamespace(self::$config->middleware->namespace);
        Middleware::setPath(self::root(self::$config->middleware->path));
    }

    /**
    * Route
    **/
    public static function route() {
        // Eanble
        if (self::$config->router->enable) {

            // Prefix web 
            Route::setPrefixGates("web", self::$config->router->web->prefix);
            
            // Prefix API 
            Route::setPrefixGates("api", self::$config->router->api->prefix);

            // Controller namespace
            Route::setControllerNamespace(Controller::getNamespace());

            // Controller default method
            Route::setControllerDefaultMethod(Controller::getDefaultMethod());

            // Middleware namespace
            Route::setMiddlewareNamespace(Middleware::getNamespace());

            // Middleware method
            Route::setMiddlewareMethod(Middleware::getMethod());

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
            }, self::$config->router->web->routes);

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
                }, self::$config->router->api->routes);
            });

            // Run
            if(PHP_SAPI != 'cli'){ 
                Route::run();
            }
        }
    }
    
    /**
    * Event
    **/
    public static function event() {
        Event::setNamespace(self::$config->events->namespace);
        Event::setPath(self::$config->events->path);
    }

    /**
    * Notifi
    **/
    public static function notifi() {
        Notifi::setPdo(pdoDB::connection());
        Notifi::setLoginSession(self::$config->db->auth->loginSession);
        Notifi::setTable(self::$config->notifications->table);
        Notifi::setUserTable(self::$config->db->auth->table);
    }

    /**
    * Response
    **/
    public static function response() {
        Response::filesystemRoot(self::root());
    }

    /**
    * Crawl
    **/
    public static function crawl() {
        Crawl::filesystemRoot(self::root());
    }

    /**
    * Cross-Origin Resource Sharing
    **/
    public static function cors() {
        if(self::$config->cors->enable === true || trim(self::$config->cors->enable == 'true')){
            Cors::enable();
        }
    }
     
    /**
    * Helpers
    **/
    public static function helpers() {
        #--------------------------------------------------
        # Helpers for debugger

        #--------------------------------------------------
        /**
         * Page
         *
        */
        Debugger::xCommand("page", function(string $path, string $code){
            Debugger::viewCode(View::html($path), $code);
        });

        #--------------------------------------------------

        # Helpers for routes

        #--------------------------------------------------
        /**
         * Page
         *
        */
        Route::xCommand("page", function($uri, $page, $data=[], $method='get', $options=[]){
            return Route::{$method}($uri, function () use ($page, $data) {
                return View::page($page, $data);
               }, $options);
        });

        #-------------------------------------------------- 

        # Helpers for inputs

        #--------------------------------------------------
        /**
         * Validate
         *
        */
        Input::xCommand("validate", function(array $rules, array $message = []){
            return Validator::validate(Input::all(), $rules, $message);
        });

        #-------------------------------------------------- 
        
        # Helpers for views

        #--------------------------------------------------
        /**
         * Blob
         *
        */
        View::directive("blob",function($arg){
            return ('data:;base64,'.base64_encode($arg));
        });
        #--------------------------------------------------

        #--------------------------------------------------
        /**
         * Limit
         *
        */
        View::directive("limit",function($arg, $limit, $symbol='...'){
            return substr_replace($arg, $symbol, $limit);
        });
        #--------------------------------------------------

        #--------------------------------------------------
        /**
         * Lower
         *
        */
        View::directive("lower",function($arg){
            return strtolower($arg);
        });
        #--------------------------------------------------

        #--------------------------------------------------
        /**
         * Substr
         *
        */
        View::directive("substr",function($arg, $frist, $second=1) {
            return substr($arg, $frist, $second);
        });
        #--------------------------------------------------

        #--------------------------------------------------
        /**
         * Upper
         *
        */
        View::directive("upper",function($arg) {
            return strtoupper($arg);
        });
        #-------------------------------------------------- 

        # Helpers for emails

        #--------------------------------------------------
        /**
         * Page
         *
        */
        Mail::xCommand("page", function($page, $data=[]){
            return Mail::message(View::html($page, $data))->html();
        });
        #--------------------------------------------------   
    }
    
}