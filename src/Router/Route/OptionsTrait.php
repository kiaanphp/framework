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
    protected function setOptions($options, string $key=null, string|array $value=null){
      if(is_null($key)){
        $this->options = [
          "prefix" => isset($options['prefix']) ? $this->options['prefix'] . trim($options['prefix']) : null,
          "suffix" => isset($options['suffix']) ? $this->options['suffix'] . trim($options['suffix']) : null,
          "name" => isset($options['name']) ? $this->options['name'] . trim($options['name']) : null,
          "namespace" => isset($options['namespace']) ? $this->options['namespace'] . trim($options['namespace']) : null,
          "middleware" => isset($options['middleware']) ? ((is_array($options['middleware'])) ? array_merge($this->options['middleware'] ,$options['middleware']) : array_merge($this->options['middleware'] ,array($options['middleware']))) : array(),
        ];
      }else{
        $this->options = [
          "prefix" => ($key=='prefix') ? $this->options['prefix'] . trim($value) : null,
          "suffix" => ($key=='suffix') ? $this->options['suffix'] . trim($value) : null,
          "name" => ($key=='name') ? $this->options['name'] . trim($value) : null,
          "namespace" => ($key=='namespace') ? $this->options['namespace'] . trim($value) : null,
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
          "namespace" => null,
          "middleware" => array(),
      ];
    }

     /**
      * Group
      *
      * @param string $value
      */
      public function group(array $options, callable $callback) {
        // Options
        $this->setOptions($options);

        if (is_callable($callback)) {
            call_user_func($callback);
        } else {
            throw new \BadFunctionCallException("Valid callback function!");
        }

        // Options defaults
        $this->setOptionsDefaults();

        return $this;
     }

}