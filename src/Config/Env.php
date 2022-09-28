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
   
    /*
    * Global traits
    *
    */
    use \Kiaan\Application\Resources\Global\FilesystemPathTrait;
    use \Kiaan\Application\Resources\Global\ExtendingTrait;

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

        return true;
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
     * Get environment variable by the given key
     *
     */
    public function get($key, $default=null) {
        return isset($_ENV[$key]) ? $_ENV[$key] : $default;
    }

    /**
     * Delete environment variable by the given key
     *
     */
    public function delete($key) {
        unset($_ENV[$key]);
        putenv("$key=");
        putenv("$key");

        return true;
    }

    /**
     * Change key name of environment variable
     * 
    */
    public function key($key, $newKey) {
        $value = $this->get($key);
        $this->set($newKey, $value);

        return $this->delete($key);
    }

    /**
     * Destroy all data
     *
    */
    public function destroy() {
        foreach($_ENV as $key=>$env){
            putenv("$key=");
            putenv("$key");
        }

        return $_ENV = array();
    }

    /**
     * Load content
     * 
    */
    public function load(){
        return new Env\Loader($this);
    }

    /**
     * Load file
     * 
    */
    public function file($path) {
        return $this->load()->file($path);
    }

    /**
     * Load content
     * 
    */
    public function content($content) {
        return $this->load()->content($content);
    }
    
    /**
     * Return array for all data
    * 
    */
    public function toArray() {
        return $_ENV;
    }

    /**
     * Return object for all data
    * 
    */
    public function toObject() {
        return json_decode(json_encode($this->toArray()), false);
    }
  
    /**
     * Return object for all data
     * 
     */
    public function all() {
        return $this->toObject();
    }
    
}