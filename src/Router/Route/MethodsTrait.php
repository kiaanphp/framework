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
namespace Kiaan\Router\Route;

/*
|---------------------------------------------------
| Methods Trait
|---------------------------------------------------
*/
trait MethodsTrait {

    /**
     * Add new method
     *
     * @param string $name
     * @param string $uri
     * @param callback $callback
     * @param array $options
     */
    protected function addToMethods($name, $uri, $callback, $options = []) {
        // Options Cache
        $optionsCache = $this->options;
        
        $options = [
            "prefix" => $options['prefix'] ?? '',
            "suffix" => $options['suffix'] ?? '',
            "name" => $options['name'] ?? '',
            "namespace" => $options['namespace'] ?? '',
            "middleware" => $options['middleware'] ?? array(),
        ];

        // Options
        if(!empty($options)){
            $this->setOptions($options);
        }

        // Add
        $this->add($name, $uri, $callback, $options);

        // Options defaults
        $this->setOptions($optionsCache);
    }
    
    /**
     * Add new get method
     *
     * @param string $uri
     * @param callback $callback
     * @param array $options
     */
    public function get($uri, $callback, $options = []) {
        $this->addToMethods('get', $uri, $callback, $options);

        return $this;
    }

    /**
     * Add new post method
     *
     * @param string $uri
     * @param callback $callback
     * @param array $options
     */
    public function post($uri, $callback, $options = []) {
        $this->addToMethods('post', $uri, $callback, $options);

        return $this;
    }
 
    /**
     * Add new delete method
     *
     * @param string $uri
     * @param callback $callback
     * @param array $options
     */
    public function delete($uri, $callback, $options = []) {
        $this->addToMethods('delete', $uri, $callback, $options);
                
        return $this;
    }

     /**
     * Add new put method
     *
     * @param string $uri
     * @param callback $callback
     * @param array $options
     */
    public function put($uri, $callback, $options = []) {
        $this->addToMethods('put', $uri, $callback, $options);
                
        return $this;
    }

    /**
     * Add new patch method
     *
     * @param string $uri
     * @param callback $callback
     * @param array $options
     */
    public function patch($uri, $callback, $options = []) {
        $this->addToMethods('patch', $uri, $callback, $options);
                
        return $this;
    }

    /**
     * Add new copy method
     *
     * @param string $uri
     * @param callback $callback
     * @param array $options
     */
    public function copy($uri, $callback, $options = []) {
        $this->addToMethods('copy', $uri, $callback, $options);
                
        return $this;
    }

    /**
     * Add new options method
     *
     * @param string $uri
     * @param callback $callback
     * @param array $options
     */
    public function options($uri, $callback, $options = []) {
        $this->addToMethods('options', $uri, $callback, $options);
                
        return $this;
    }

    /**
     * Add new lock method
     *
     * @param string $uri
     * @param callback $callback
     * @param array $options
     */
    public function lock($uri, $callback, $options = []) {
        $this->addToMethods('lock', $uri, $callback, $options);
                
        return $this;
    }

    /**
     * Add new unlock method
     *
     * @param string $uri
     * @param callback $callback
     * @param array $options
     */
    public function unlock($uri, $callback, $options = []) {
        $this->addToMethods('unlock', $uri, $callback, $options);
                
        return $this;
    }

    /**
     * Add new propfind method
     *
     * @param string $uri
     * @param callback $callback
     * @param array $options
     */
    public function propfind($uri, $callback, $options = []) {
        $this->addToMethods('propfind', $uri, $callback, $options);
                
        return $this;
    }

}



