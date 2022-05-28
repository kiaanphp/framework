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
namespace Kiaan\Config;

/*
|---------------------------------------------------
| Environment variables
|---------------------------------------------------
*/
class Env {

    /**
     *  Traits
     * 
    */
    use Env\FileSystemTrait;

    /**
     * Construct
     * 
    */
    public function __construct(){}

    /**
     * Set new environment variable
     *
     * @param string $key
     * @param string $value
     *
     */
    public function set($key, $value) {
        $_ENV[$key] = $value;
        putenv(sprintf('%s=%s', $key, $value));
    }
   
    /**
     * Check that environment variable has the key
     *
     * @param string $key
     *
     */
    public function has($key) {
        return isset($_ENV[$key]);
    }

    /**
     * Get
     *
     * Get environment variable by the given key
     */
    public function get($key) {
        return isset($_ENV[$key]) ? $_ENV[$key] : null;
    }

    /**
     * Delete
     *
     * Delete environment variable by the given key
     */
    public function delete($key) {
        // $_ENV
        unset($_ENV[$key]);

        // putenv
        putenv("$key=");
        putenv("$key");

        return $_ENV;
    }

    /**
     * Name
     * 
     * Change key name of environment variable
     *
    */
    public function name($key, $name) {
        if (array_key_exists($key, $_ENV)) {
            // Get value
            $value = $_ENV[$key];

            // Delete form $_ENV
            unset($_ENV[$key]); // $_Env

            // Delete form putenv
            putenv("$key=");
            putenv("$key");

            // Set to $_ENV
            $_ENV[$name] = $value;
            putenv(sprintf('%s=%s', $name, $value));
        }
        
        return $_ENV[$name] ?? false;
    }

    /**
     * Return all
     *
     * @return array
    */
    public function all() {
        return $_ENV;
    }

    /**
     * Destroy
     *
    */
    public function destroy() {
        foreach($_ENV as $key=>$env){
            putenv("$key=");
            putenv("$key");
        }

        return $_ENV = [];
    }
    
    /**
     * Load
     * 
     * Load content
    */
    public function load(){
       $loader = new Env\Loader();
       $loader->env = $this;

       return $loader;
    }

}