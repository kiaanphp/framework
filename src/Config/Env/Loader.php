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
     * Comment symbol
     * 
    */
    protected $commentSymbol = "#";

    /**
     * Construct
     * 
    */
    public function __construct($env){
        $this->env = $env;
    }

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
        
        $regex = '/\\\\\\$|\\$\\{[^}]*\\}/';

        foreach ($lines as $line) {
            // Comments
            if (strpos(trim($line), $this->commentSymbol) === 0) { continue; }

            // Define a variable
            $key = explode('=', $line);
            $name = (isset($key[0])) ? trim($key[0]) : null;
            $value = (isset($key[1])) ? trim($key[1]) : null;

            $value = preg_replace_callback($regex, function($matches) use($result) {
                if(($matches[0])[0]=='\\'){
                    return substr($matches[0], 1);
                }

                $var = substr($matches[0], 2);
                $var = substr($var, 0, -1);
                
                return (isset($result[$var])) ? $result[$var] : '';
            }, $value);
            
            $result[$name] = $value;
        }
        
        return $result;
    }

    /**
     *  Load content from string
     * 
    */
    public function content(String $content)
    {
       $this->data = $this->parse($content);

       return clone($this);
    }

    /**
     *  Load content from file
     * 
    */
    public function file(String $path)
    {
        // Path
        $path = $this->env->filesystemRoot() . $path;

        if(!file_exists($path)) {
            throw new \InvalidArgumentException(sprintf('%s does not exist', $path));
        }

        // Content
        $content = file_get_contents($path);
        
        // Load
        $this->data = $this->parse($content);

        return clone($this);
    }

    /*
    * Add to $_Env and putenv()
    *
    */
    public function submit() {
        $data = $this->data;
        
        foreach ($data as $key => $value) {
            $_ENV[$key] = $value;
            putenv(sprintf('%s=%s', $key, $value));
        }

        // Reset
        $this->data = array();
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

        return clone($this);
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
     * Delete environment variable by the given key
     *
     */
    public function delete($key) {
        unset($this->data[$key]);

        return clone($this);
    }

    /**
     * Change key name of environment variable
     * 
    */
    public function key($key, $newKey) {
        $value = $this->get($key);
        $this->set($newKey, $value);
        $this->delete($key);

        return clone($this);
    }

    /**
     * Destroy all data
     *
    */
    public function destroy() {
        $this->data = array();

        return clone($this);
    }

    /**
     * Get environment variable by the given key
     *
     */
    public function get($key, $default=null) {
        return isset($this->data[$key]) ? $this->data[$key] : $default;
    }

    /**
     * Return array for all data
    * 
    */
    public function toArray() {
        return $this->data;
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