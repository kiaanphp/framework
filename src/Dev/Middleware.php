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
namespace Kiaan\Dev;

/*
|---------------------------------------------------
| Middleware
|---------------------------------------------------
*/
class Middleware {
    
    /*
    * Global traits
    *
    */
    use \Kiaan\Application\Resources\Global\ExtendingTrait;
     
    /**
    * Middleware namespace
    *
    */
    protected $namespace;

    /**
    * Middleware path
    *
    */
    protected $path;

    /**
    * Method
    *
    */
    protected $method = 'handle';

    /**
     * Get middleware namespace
     * 
    */
    public function getNamespace() {
        return $this->namespace;
    }

    /**
     * Set middleware namespace
     * 
    */
    public function setNamespace($value) {
        $this->namespace = $value;

        return clone($this);
    }

    /**
     * Get middleware path
     * 
    */
    public function getPath() {
        return $this->path;
    }

    /**
     * Get middleware path
     * 
    */
    public function setPath($path) {
        return $this->path = $path;
    }

    /**
     * Get method
     * 
    */
    public function getMethod() {
        return $this->method;
    }

    /**
     * Call
     * 
    */
    public function call($callback, $params=[]) {
        // Callback
        if(is_array($callback)){
            $callback = $callback;
        }else{
            $callback = explode(',', $callback);
            $callback = array_map('trim', $callback);            
        }
        
        // Class
        $class = $callback[0];

        if (!class_exists($callback[0])) {
            $class = trim($this->MiddlewareNamespace . '\\' . $callback[0], '\\');
            $class = str_replace(['.', '/', '//', '\\\\'],"\\" ,$class);

            $class = trim($this->MiddlewareNamespace . '\\' . $class);
        }

        // Method
        $method = $callback[1] ?? $this->defaultMethod;

        // Call callback
        if (class_exists($class)) {
            $object = new $class;
            if (method_exists($object, $method)) {
                return call_user_func_array([$object, $method], $params);
            } else {
                throw new \Exception("The method " . $method . " is not exists at " . $class);
            }
        } else {
            throw new \Exception("Class " . $class . " is not found");
        }   

    }

}
