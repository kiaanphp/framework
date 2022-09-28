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
namespace Kiaan\Exceptions;

/*
|---------------------------------------------------
| Debugger
|---------------------------------------------------
*/
class Debugger {
    
    /*
    * Global traits
    *
    */
    use \Kiaan\Application\Resources\Global\FilesystemPathTrait;
    use \Kiaan\Application\Resources\Global\ExtendingTrait;
     
    /**
    * Traits
    *
    */
    use Debugger\Helpers;

    /**
    * Debug enable
    **/
    protected $enable = true;

    /**
    * Errors view
    *
    **/
    protected $errors_view = [
        '404' => 'Debugger\\resources\\404.php',
        '500' => 'Debugger\\resources\\500.php'
    ];

    /**
    * Set error page
    *
    **/
    public function view($path, $code) {
        if(!is_null($path)){
            $path = $this->filesystemRoot() . $path;
        }

        $path = (is_null($path)) ? "Debugger\\resources\\$code.php" : $path;
        
        $this->errors_view[$code] = $path;

        return true;
    }

    /**
    * Set error page by source code
    *
    **/
    public function viewCode(string $source_code, $code) {
        $path = "Debugger\\resources\\customize_$code.php";
        
        $this->errors_view[$code] = $path;

        file_put_contents(__DIR__.DIRECTORY_SEPARATOR.$path, $source_code);

        return true;
    }

    /*
    * Run
    *
    */
    public function run() {
        // Reporting
        ini_set('display_errors', 'Off');
        ini_set('display_startup_errors', 'Off');
        error_reporting(-1);

        // Shutdown function
        register_shutdown_function([$this, 'shutDown']);
    }

    /**
    * ShutDown
    *
    **/
    public function shutDown() {
        // Get Error
        $error = error_get_last();
        $response = http_response_code();

        // Response
        switch($response) {
            case 404:
                if(PHP_SAPI != 'cli'){
                    ob_end_clean(); 
                    include_once $this->errors_view['404'];
                    exit();
                }else{
                    $msg = "Sorry, Whoops looks like something went wrong.";
                    fwrite(STDERR, "\033[0;31m $msg \033[0m".PHP_EOL);
                }
        }

        // Run
        if($error){
            if($this->enable){
                // HTTP response code: 500
                http_response_code(500);

                // Clean content
                ob_end_clean(); 

                // Error
                $error = (object) [
                    'number' => $error['type'],
                    'file' => $error['file'],
                    'line' => $error['line'],
                    'type' => $this->error_type($error['type']),
                    'message' => nl2br(htmlspecialchars($error['message'])),
                    'content' => explode("<br />", highlight_file($error['file'], true)),
                ];

                // View
                if(PHP_SAPI != 'cli'){
                    include_once "Debugger\\resources\\debugger.php";
                }else{
                    $msg = "Sorry, Whoops looks like something went wrong.";
                    fwrite(STDERR, "\033[0;31m $msg \033[0m".PHP_EOL);
                }
                // Exit
                exit();
            }else{
                    // 500 page
                    if(PHP_SAPI != 'cli'){
                        include_once $this->errors_view['500'];
                    }else{
                        $msg = "Sorry, Whoops looks like something went wrong.";
                        fwrite(STDERR, "\033[0;31m $msg \033[0m".PHP_EOL);
                    }
                }
        }

    }

    /**
    * Error
    *
    **/
    public function error($value, $code=500) {
        if(PHP_SAPI != 'cli'){
            http_response_code($code);

            throw new \Exception($value);
        }else{
            fwrite(STDERR, "\033[0;31m $value \033[0m".PHP_EOL);
        }
    }
    
}