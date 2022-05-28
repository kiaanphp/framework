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

    /**
    * Temp discovery file
    *
    */
    protected $tempDiscoveryFile;

    /**
    * Middleware namespace
    *
    */
    protected $namespace;

    /**
    * Default namespace
    *
    */
    protected $defaultMethod;

    /**
     * Construct
     * 
    */
    public function __construct($namespace, $defaultMethod){
        $this->setNamespace($namespace);
        $this->setDefaultMethod($defaultMethod);

        // Temp discovery file
        $this->tempDiscoveryFile = __DIR__.DIRECTORY_SEPARATOR.'Middleware'.DIRECTORY_SEPARATOR.'Resources'.DIRECTORY_SEPARATOR.'TempDiscovery.php';
    }

    /**
     * Get Middleware namespace
     * 
    */
    public function getNamespace() {
        return $this->namespace;
    }

    /**
     * Set Middleware namespace
     * 
    */
    public function setNamespace($value) {
        $this->namespace = $value;

        return $this;
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

        return $this;
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
