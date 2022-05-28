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
| Use
|---------------------------------------------------
*/
use Kiaan\Config\Config\Loader;

/*
|---------------------------------------------------
| Configurations
|---------------------------------------------------
*/
class Config {

    /**
     *  Traits
     * 
    */
    use Config\FileSystemTrait;

    /**
     *  Cache
     * 
    */
    protected $cache = [];

    /**
     * Construct
     *
    */
    public function __construct(){}

    /**
     *  Load
     * 
     *  Load content from string
    */
    protected function load($content) {

      if(is_array($content)){
        $data = new Loader($content);
        $path = null;
      }else{

        if (!is_file($content)) {

            // file with prefix
            $path = $this->preparePathFileSystem($content).".php";

            if (is_file($path)) {
                // load
                $data = new Loader(include($path));
            } else {
                throw new \Exception("File not found! ('$path') ");
            }

        }else {

          // file without prefix
          $data = new Loader(include($content));
          $path = $content;
          
        }

      }

      // Check in cache
      $cache_path = array_column($this->cache, 'path');
      if(in_array($path, $cache_path)){
        $index = array_search($path, $cache_path);
        $data = $this->cache[$index]['data'];

        return $data;
      }

      // Cache
      $cache = [[
        "path" => $path,
        "data" => $data
      ]];
      
      $this->cache = array_merge($this->cache, $cache);

      // Return 
      return $data;

    }

    /**
     *  All
     * 
    */
    public function all($content) {
      return ($this->load($content))->all();
    }

    /**
     *  Get
     * 
    */
    public function get($content, $key, $vars=[], $default=null) {
      return ($this->load($content))->get($key, $vars, $default);
    }

    /**
     *  Set
     * 
    */
    public function set($content, $key, $value) {
      return ($this->load($content))->set($key, $value);
    }

    /**
     *  Has
     * 
    */
    public function has($content, $key) {
      return ($this->load($content))->has($key);
    }

}