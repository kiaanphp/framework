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
namespace Kiaan\Config\Env;

/*
|---------------------------------------------------
| Loader
|---------------------------------------------------
*/
class Loader {

    /**
     * ENV
     * 
    */
    public $env;

    /**
     *  data
     * 
    */
    protected $data;

    /**
     * Construct
     * 
    */
    public function __construct(){}

    /**
     * Parse
     * 
    */
    protected function parse(string $content){
        $content = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $content);
        $content = chop($content);

        $lines = preg_split("/\r\n|\n|\r/", $content);
        array_shift($lines);

        $result = array();
        foreach ($lines as $line) {

            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            $name = trim(explode('=', $line)[0]);
            $value = trim(explode('=', $line)[1] ?? null);

            $result[$name] = trim($value);
        }
        
        return $result;
    }

    /*
    * Reset
    * 
    */
    protected function _reset() {
        $this->data = '';
    }

    /**
     *  Content
     * 
     *  Load content from string
    */
    public function content(String $content)
    {
       $this->data = $this->parse($content);

       return $this;
    }

    /**
     *  File
     * 
     *  Load content from file
    */
    public function file(String $path)
    {
        // Path
        $path = $this->env->preparePathFileSystem($path);

        if(!file_exists($path)) {
            throw new \InvalidArgumentException(sprintf('%s does not exist', $path));
        }

        // Content
        $content = file_get_contents($path);
        
        // Load
        $this->data = $this->parse($content);

        return $this;
    }

    /*
    * Submit
    
    * to $_Env and putenv()
    */
    public function submit() {
        $data = $this->data;
        
        foreach ($data as $key => $value) {
            $_ENV[$key] = $value;
            putenv(sprintf('%s=%s', $key, $value));
        }

        // Reset
        return $this->_reset();
    }
    
    /**
     * Return all
     *
     * @return array
    */
    public function all() {
        return $this->data;
    }

    /**
     * Set new environment variable
     *
     * @param string $key
     * @param string $value
     *
     */
    public function set($key, $value) {
        $this->data[$key] = $value;

        return $this;
    }

    /**
     * Check that environment variable has the key
     *
     * @param string $key
     *
     */
    public function has($key) {
        return isset($this->data[$key]);
    }

    /**
     * Get
     *
     * Get environment variable by the given key
     */
    public function get($key) {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    /**
     * Delete
     *
     * Delete environment variable by the given key
     */
    public function delete($key) {
        // $_ENV
        unset($this->data[$key]);

        return $this;
    }

    /**
     * Name
     * 
     * Change key name of environment variable
     *
    */
    public function name($key, $name) {
        if (array_key_exists($key, $this->data)) {
            // Get value
            $value = $this->data[$key];

            // Delete
            unset($this->data[$key]);


            // Set
            $this->data[$name] = $value;
        }
        
        return $this;
    }

    /**
     * Destroy
     *
    */
    public function destroy() {
        $this->data = array();

        return $this;
    }

}