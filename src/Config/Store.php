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
| Store
|---------------------------------------------------
*/
class Store {

    /*
    * Global traits
    *
    */
    use \Kiaan\Application\Resources\Global\ExtendingTrait;

    /**
     *  Data
     * 
    */
    public $data = [];

    /**
     *  Mutation
     * 
    */
    public $mutation = [];

    /**
     * Get
     * 
    */
    public function __get($key) {
      return $this->get($key);
    }

    public function get($key, $default=null) {
      $key = trim($key);

      if(isset($this->data[$key])){
        if(is_callable($this->data[$key])){
          $result = call_user_func_array($this->data[$key], array($this));
        }else{
          $result = $this->data[$key];
        }
      }else{
        $result = $default;
      }

      return $result;
    }
    
    /**
     * Set
     * 
     */
    public function __set($key, $value) {
      $key = trim($key);

      return $this->set($key, $value);
    }

    public function set($key, $value) {
      $key = trim($key);

      return $this->data[$key] = $value;
    }

    /**
     * Has
     * 
    */
    public function has($key) {
      $key = trim($key);

      return isset($this->data[$key]);
    }

    /**
     * Delete
     * 
     */
    public function delete($key) {
      $key = trim($key);

      if(isset($this->data[$key])){
        unset($this->data[$key]);
      }
      
      return true;
    }

    /**
     * Destroy
     * 
     */
    public function destroy() {
      return $this->data = array();
    }
    
    /**
     * Key
     * 
     */
    public function key($key, $newKey) {
      $key = trim($key);

      if(isset($this->data[$key])){
        $value = $this->data[$key];
        
        unset($this->data[$key]);
        
        $this->data[$newKey] = $value;
      }
      
      return true;
    }

    /**
     * To array
     * 
     */
    public function toArray() {
      $result = array();

      foreach($this->data as $key => $data){
        if(is_callable($data)){
          $result[$key] = call_user_func_array($this->data[$key], array($this));
        }else{
          $result[$key] = $data;
        }
      }

      return $result;
    }
    /**
     * To object
     * 
     */
    public function toObject() {
      return json_decode(json_encode($this->toArray()), false);
    }

    /**
     * All
     * 
     */
    public function all() {
      return $this->toObject();
    }

  }