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
use Kiaan\Config\Config\Database;

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
    use Config\ConfigSystemTrait;
    
    /*
    * Global traits
    *
    */
    use \Kiaan\Application\Resources\Global\FilesystemPathTrait;
    use \Kiaan\Application\Resources\Global\ExtendingTrait;

    /**
     *  Cache
     * 
    */
    public $cache = [];

    /**
     *  Database Cache
     * 
    */
    public $db_cache = [];
    
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
     *  Load
     * 
     *  Load content from string
    */
    protected function load($content) {
        // file with prefix
        $path = $this->filesystem_path() . $content . '.php';

        if (is_file($path)) {
            // load
            $data = new Loader(include($path));
        } else {
            throw new \Exception("File not found! ('$path') ");
        }

      return $this->prepare_load_content($path, $data);
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
     *  Database
     * 
    */
    public function db() {
      // PDO
      $pdo = $this->getPdo();

      // Table
      $table = $this->getTable();

      // Initialize
     return new Database($this, $pdo, $table);
    }

    /**
     * Get
     * 
    */
    public function get($content, $key, $default=null) {
      return ($this->load($content))->get($key, array(), $default);
    }

    /**
     * Set
     * 
    */
    public function set($content, $key, $value) {
      return ($this->load($content))->set($key, $value);
    }

    /**
     * Has
     * 
    */
    public function has($content, $key) {
      return ($this->load($content))->has($key);
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
      return ($this->load($content))->key($key, $newKey);
    }

    /**
     * Return array for all data
    * 
    */
    public function toArray($content) {
      return ($this->load($content))->toArray();
    }

    /**
     * Return object for all data
    * 
    */
    public function toObject($content) {
      return ($this->load($content))->toObject();
    }

    /**
     * Return object for all data
     * 
     */
    public function all($content) {
        return ($this->load($content))->all();
    }

}