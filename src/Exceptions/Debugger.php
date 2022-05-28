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
        '403' => 'Debugger\\resources\\403.php',
        '404' => 'Debugger\\resources\\404.php',
        '500' => 'Debugger\\resources\\500.php'
    ];

    /**
    * Get errors pages
    *
    **/
    public function getPages() {
        return $this->errors_view;
    }

    /**
    * Set errors pages
    *
    **/
    public function setPages(array $errors_view) {
        return $this->errors_view = $errors_view;
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

        // Handle
        register_shutdown_function([$this, 'ShutDown']);
    }

    /**
    * ShutDown
    *
    **/
    public function ShutDown() {
        // Get Error
        $error = error_get_last();
        $response = http_response_code();

        // Response
        switch($response) {
            /*
            case 403:
                if(PHP_SAPI != 'cli'){
                    ob_end_clean(); 
                    include_once $this->errors_view['403'];
                    exit();
                }else{
                    $msg = "Sorry, Whoops looks like something went wrong.";
                    fwrite(STDERR, "\033[0;31m $msg \033[0m".PHP_EOL);
                }

            */
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
    public function error($value) {
        if(PHP_SAPI != 'cli'){
            throw new \Exception($value);
        }else{
            fwrite(STDERR, "\033[0;31m $value \033[0m".PHP_EOL);
        }
    }
    
}