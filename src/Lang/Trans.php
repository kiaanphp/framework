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
namespace Kiaan\Lang;

/*
|---------------------------------------------------
| Translation
|---------------------------------------------------
*/
class Trans {

    /**
     *  Traits
     * 
    */
    use Trans\FileSystemTrait;

    /**
     *  Cache
     * 
    */
    protected $cache = [];

    /**
     * Construct
     * 
    */
    public function __construct($local=null){
      // local
      if (!is_null($local)) {
        $this->setLocal($local);
      }
    }

    /**
      * Get local(language)
      * 
    */
    public function getLocal() {
        return $this->local;
    }

    /**
    * Set local(language)
    * 
    */
    public function setLocal($lang) {
        return $this->local = $lang;
    }

    /**
    * Is local(language)
    * 
    */
    public function isLocal($lang) {
      if($this->local != $lang){
        return false;
      }

      return true;
    }

    /**
     *  Load
     * 
     *  Load content from string
    */
    public function load($content, $lang=null) {
      // Local
      $local = (is_null($lang)) ? $this->local : $lang ;

      if (!is_file($content)) {

      // file with prefix
      $path = $this->preparePathFileSystem($content, $local).".php";

      if(is_file($path)){
        // load
        $data = new Trans\Loader(include($path));
      }else{
        throw new \Exception("File not found! ('$path') ");
      }

      }else {

        // file without prefix
        $data = new Trans\Loader(include($content));
        
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
    public function all($content, $lang=null) {
        return ($this->load($content, $lang))->all();
    }

    /**
     *  Get
     * 
    */
    public function get($content, $key, $vars=[], $default=null, $lang=null) {
        return ($this->load($content, $lang))->get($key, $vars,$default);
    }

    /**
     *  Set
     * 
    */
    public function set($content, $key, $value, $lang=null) {
        return ($this->load($content, $lang))->set($key, $value);
    }

    /**
     *  Has
     * 
    */
    public function has($content, $key, $lang=null) {
        return ($this->load($content, $lang))->has($key);
    }

    /**
     * Detect
     * 
     * get detect browser language
    */
    public function detect() {
        return substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) ?? null;
    }

    /**
     * Suggestion
     * 
     * get suggestion browser language
    */
    public function suggestion() {
        return substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 15, 2) ?? null;
    }

}