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
| Uses
|---------------------------------------------------
*/
use Kiaan\Lang\Trans\Loader;

/*
|---------------------------------------------------
| Translation
|---------------------------------------------------
*/
class Trans {
    
    /*
    * Global traits
    *
    */
    use \Kiaan\Application\Resources\Global\ExtendingTrait;
    use \Kiaan\Application\Resources\Global\FilesystemPathTrait;

    /**
     *  Cache
     * 
    */
    protected $cache = [];

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
      protected function load($content, $lang=null) {
        // Local
        $local = (is_null($lang)) ? $this->local : $lang ;

        // file with prefix
        $path = $this->filesystemPath() . DIRECTORY_SEPARATOR . $local . DIRECTORY_SEPARATOR . $content . ".php";

        if (is_file($path)) {
            // load
            $data = new Loader(include($path));
        } else {
            throw new \Exception("File not found! ('$path') ");
        }

      return $this->prepare_load_content($path, $data);
    }

    /**
     *  Prepare Load content
     * 
    */
    protected function prepare_load_content($path, $data) {
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
     *  Load from file
     * 
    */
    public function file(string $path) {
      // extension
      if(empty(pathinfo($path, PATHINFO_EXTENSION))){
        $ext = '.php';
      }else{
        $ext = '';
      };

      $path = $this->filesystem_root() . $path . $ext;

      if(!is_file($path)){
        throw new \Exception("File not found! ('$path') ");
      }

      $data = new Loader(include($path));

      return $this->prepare_load_content($path, $data);
    }

    /**
     *  Load from file
     * 
    */
    public function content(array $array) {
      if(is_array($array)){
        $data = new Loader($array);
        $path = null;
      }else{
        throw new \Exception("Not array!");
      }

      return $this->prepare_load_content($path, $data);
    }

    /**
     *  Get
     * 
    */
    public function get($content, $key, $default=null, $lang=null) {
        return ($this->load($content, $lang))->get($key, array(),$default);
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

    /**
     * Delete
     * 
    */
    public function delete($content, $key) {
      return ($this->load($content))->delete($key);
    }

    /**
     * Destroy
     * 
    */
    public function destroy($content) {
      return ($this->load($content))->destroy();
    }

    /**
     * Key
     * 
    */
    public function key($content, $key, $newKey) {
      return ($this->load($content))->name($key, $newKey);
    }

    /**
     * Return array for all data
    * 
    */
    public function toArray($content, $lang=null) {
      return ($this->load($content, $lang))->toArray();
    }

    /**
     * Return object for all data
    * 
    */
    public function toObject($content, $lang=null) {
      return ($this->load($content, $lang))->toObject();
    }

    /**
     * Return object for all data
     * 
     */
    public function all($content, $lang=null) {
      return ($this->load($content, $lang))->all();
    }

}