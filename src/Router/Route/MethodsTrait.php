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
            "controller" => $options['controller'] ?? '',
            "middleware" => $options['middleware'] ?? array(),
        ];

        // Options
        if(!empty($options)){
            $this->setOptions($options);
        }

        // Optional parameters
        $startTag = $this->route_optional_parameter[0];
        $endTag = $this->route_optional_parameter[1];
        $pattern = "#\s*\\$startTag.+\\$endTag\s*#U";
        $uri_pattern = explode(":", $uri)[0];

        if(preg_match($pattern, $uri_pattern)){
            return $this->addToMethodsOptionalParameters($name, $uri, $callback, $options);
        }

        // Add
        $this->add($name, $uri, $callback, $options);

        // Options defaults
        $this->setOptions($optionsCache);

        // Set route as group
        if($this->routes_in_group && !$this->routes_not_in_group_toggle){
            $this->routes_in_group = false; 
            $this->routes_not_in_group_toggle = true; 
        }
        if(!$this->routes_in_group){
            $this->route = array_key_last($this->routes); 
        }else{
            $this->route[] = array_key_last($this->routes);
        }
    }

    protected function addToMethodsOptionalParameters($name, $uri, $callback, $options) {
        // Without
        $startTag = $this->route_optional_parameter[0];
        $endTag = $this->route_optional_parameter[1];
        $pattern = "#\s*\\$startTag.+\\$endTag\s*#U";

        $url = preg_replace($pattern, '', $uri);
        $url = preg_replace('~/+~', '/', $url);
        $this->addToMethods($name, $url, $callback, $options);

        // With
        $url = str_replace(['[', ']'], ['{', '}'], $uri);
        $this->addToMethods($name, $url, $callback, $options);
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

        return clone($this);
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

        return clone($this);
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
                
        return clone($this);
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
                
        return clone($this);
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
                
        return clone($this);
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
                
        return clone($this);
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
                
        return clone($this);
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
                
        return clone($this);
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
                
        return clone($this);
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
                
        return clone($this);
    }

}



