<?php

/**
 * Kiaan framework
 *
 * @author Hassan Kerdash kerdashhassan@gmail.com
**/

/*
|---------------------------------------------------
| Namespaces
|---------------------------------------------------
*/
namespace Kiaan;

/*
|---------------------------------------------------
| Facade
|---------------------------------------------------
*/
trait Facade {

    /*
    * Static facade instance
    *
    */
    protected static $__static_facade_instance;

    /*
    * Non static facade instance
    *
    */
    protected $__non_static_facade_instance;

    /*
    * Construct
    *
    */
    public function __construct()
    {
        // Get arguments
        $arguments = func_get_args();
        
        // Class instance
        $classInstance = $this->__facade();
        $classInstance = new $classInstance(...$arguments);

        // Instance (non-static)
        $this->__non_static_facade_instance = $classInstance;

        // Check if instance is already exists (static)    
        if(self::$__static_facade_instance == null) {
        return self::$__static_facade_instance = $classInstance;
        }

        // return instance
        return $classInstance;
    }

    /**
     * Return Class
     * 
     * @return mixed
     */
    abstract function __facade();

    /**
     * Calls static
     * @param string $name
     * @param array $arguments
     *
     * @return mixed
     */
    public static function __callStatic(string $name, array $arguments)
    {
            // Check if instance is already exists      
            if(self::$__static_facade_instance == null) {
                try {
                    self::$__static_facade_instance = new static;
                } catch (\Throwable $th) {}
            }
            
            return self::$__static_facade_instance->$name(...$arguments);
    }

    /**
     * Calls non-static
     * @param string $name
     * @param array $arguments
     *
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        return $this->__non_static_facade_instance->$name(...$arguments);
    }

    /*
    * Get
    */
    function __get($name){
        return $this->__non_static_facade_instance->$name;
    }

    /*
    * Set
    */
    public function __set($name, $value) {
        return $this->__non_static_facade_instance->$name = $value;
    }

    /*
    * Clone
    */
    public function __clone(){
        return self::$__static_facade_instance;
    }

}