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
| Controller
|---------------------------------------------------
*/
class Controller {
    
    /*
    * Global traits
    *
    */
    use \Kiaan\Application\Resources\Global\ExtendingTrait;
     
    /**
    * Controller namespace
    *
    */
    protected $namespace;

    /**
    * Controller path
    *
    */
    protected $path;

    /**
    * Default namespace
    *
    */
    protected $defaultMethod;

    /**
     * Get controller namespace
     * 
    */
    public function getNamespace() {
        return $this->namespace;
    }

    /**
     * Set controller namespace
     * 
    */
    public function setNamespace($value) {
        $this->namespace = $value;

        return clone($this);
    }

    /**
     * Get controller path
     * 
    */
    public function getPath() {
        return $this->path;
    }

    /**
     * Get controller path
     * 
    */
    public function setPath($path) {
        return $this->path = $path;
    }

    /**
     * Get default method
     * 
    */
    public function getDefaultMethod() {
        return $this->defaultMethod;
    }

    /**
     * Set default method
     * 
    */
    public function setDefaultMethod($value) {
        $this->defaultMethod = $value;

        return clone($this);
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
            $class = trim($this->namespace . '\\' . $callback[0], '\\');
            $class = str_replace(['.', '/', '//', '\\\\'],"\\" ,$class);

            $class = trim($this->namespace . '\\' . $class);
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
