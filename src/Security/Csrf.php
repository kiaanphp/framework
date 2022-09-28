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
namespace Kiaan\Security;

/*
|---------------------------------------------------
| Csrf
|---------------------------------------------------
*/
class Csrf {
    
    /*
    * Global traits
    *
    */
    use \Kiaan\Application\Resources\Global\ExtendingTrait;
         
    /**
     * Key
     * 
    **/
    protected $key = "_csrf";

    /**
     * Input
     * 
    **/
    protected $input = "_csrf";

    /**
     * Constructor
     * 
    **/
    public function __construct($input="_csrf", $key="_csrf") {
        $this->key = $key;
        $this->input = $input;
    }

    /*
    * Get input
    * 
    */
    public function getInput() {
        return $this->input;
    }

    /*
    * Set input
    * 
    */
    public function setInput($input) {
        return $this->input = $input;
    }

    /*
    * Get key
    *
    */
    public function getKey() {
        return $this->key;
    }
    
    /*
    * Set key
    *
    */
    public function setKey($key) {
        return $this->key = $key;
    }

    /**
     * Run
     * 
    **/
    public function run() {
        if (!isset($_SESSION[$this->key])) {
            return $_SESSION[$this->key] = bin2hex(random_bytes(24));
        }
    }

    /**
     * Create
     * 
    **/
    public function create() {
        return $_SESSION[$this->key] = bin2hex(random_bytes(24));
    }

    /**
     * Get
     * 
    **/
    public function get() {
        return $_SESSION[$this->key] ?? '';
    }

    /**
     * Delete
     * 
    **/
    public function delete() {
        unset($_SESSION[$this->key]);
    }
    
    /**
     * Check
     *
    **/
    public function check() {
        if (isset($_POST[$this->input]) && hash_equals($_SESSION[$this->key], $_POST[$this->input])) {
            return true;
          } else {
            return false;
          }
    }

}