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
| Options Trait
|---------------------------------------------------
*/
trait OptionsTrait {

    /**
     * Set options
     * 
    */
    protected function setOptions($options, string $key=null, $value=null){
      if(is_null($key)){
        $this->options = [
          "prefix" => isset($options['prefix']) ? $this->options['prefix'] . trim($options['prefix']) : null,
          "suffix" => isset($options['suffix']) ? $this->options['suffix'] . trim($options['suffix']) : null,
          "name" => isset($options['name']) ? $this->options['name'] . trim($options['name']) : null,
          "controller" => isset($options['controller']) ? $this->options['controller'] . trim($options['controller']) : null,
          "middleware" => isset($options['middleware']) ? ((is_array($options['middleware'])) ? array_merge($this->options['middleware'] ,$options['middleware']) : array_merge($this->options['middleware'] ,array($options['middleware']))) : array(),
        ];
      }else{
        $this->options = [
          "prefix" => ($key=='prefix') ? $this->options['prefix'] . trim($value) : null,
          "suffix" => ($key=='suffix') ? $this->options['suffix'] . trim($value) : null,
          "name" => ($key=='name') ? $this->options['name'] . trim($value) : null,
          "controller" => ($key=='controller') ? $this->options['controller'] . trim($value) : null,
          "middleware" => ($key=='middleware') ? array_merge($this->options['middleware'] ,$value) : array(),
        ];
      }
    }
    
    /**
     * Set options defaults
     * 
    */
    protected function setOptionsDefaults(){
      $this->options = [
          "prefix" => null,
          "suffix" => null,
          "name" => null,
          "controller" => null,
          "middleware" => array(),
      ];
    }

    /**
    * Chaining methods prepare
    *
    */
    public function chainingMethodsPrepare($method, ...$arg) {
      // Not routes
      if(is_null($this->route)){ return false; }   

      if(!$this->routes_in_group){
        // Route not in group
          $this->{$method}($this->route, ...$arg);
      }else{
        // Route in group
        foreach($this->route as $route){
          $this->{$method}($route, ...$arg);
        }
      }

      // Return
      return clone($this);
    }

    /**
    * Prefix
    *
    */
    public function prefix($value) {
      return $this->chainingMethodsPrepare("prefixPrepare", $value);
    }

    protected function prefixPrepare($route, $value) {
      // Gate
      $gate = trim($this->prefixGates[$this->routes[$route]['gate']], '/');
      $gate = empty($gate) ? $gate : "$gate/";

      // Value
      $value = trim($value, '/');

      // URI
      $uri = trim($this->routes[$route]['uri'], '/');
      $uri = "$gate$value/" . ltrim($uri, "$gate/");
    
      $this->routes[$route]['uri'] = rtrim('/' . $uri, '/');
    }

    /**
    * Suffix
    *
    */
    public function suffix($value) {
      return $this->chainingMethodsPrepare("suffixPrepare", $value);
    }

    protected function suffixPrepare($route, $value) {
      $this->routes[$route]['uri'] .= '/' . trim('/' . trim($value, '/') , '/');
    }

    /**
    * name
    *
    */
    public function name($value) {
      return $this->chainingMethodsPrepare("namePrepare", $value);
    }

    protected function namePrepare($route, $value) {
      $this->routes[$route]['options']['name'] .= trim($value);
    }

    /**
    * Middleware
    *
    */
    public function middleware($value) {
      return $this->chainingMethodsPrepare("middlewarePrepare", $value);
    }

    protected function middlewarePrepare($route, $value) {
      $this->routes[$route]['options']['middleware'][] = $value;
    }

    /**
    * Controller
    *
    */
    public function controller($value) {
      return $this->chainingMethodsPrepare("controllerPrepare", $value);
    }

    protected function controllerPrepare($route, $value) {
      $this->routes[$route]['options']['controller'] .= $value;
    }
    
     /**
      * Group
      *
      */
      public function group(callable $callback, array $options=[]) {
        // Set route as group
        $this->route = [];
        $this->routes_in_group = true;

        // Options
        $this->setOptions($options);

        if (is_callable($callback)) {
            call_user_func($callback);
        } else {
            throw new \BadFunctionCallException("Valid callback function!");
        }

        // Options defaults
        $this->setOptionsDefaults();

        // Set route as group
        $this->routes_not_in_group_toggle = false;
        
        // Return
        return clone($this);
     }

}