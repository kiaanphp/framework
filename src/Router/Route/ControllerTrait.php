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
| Controller Trait
|---------------------------------------------------
*/
trait ControllerTrait {

    /**
     * Execute controller
     * 
     */
    protected function executeController($route, $params=[]) {
        // Call function
        if (is_callable($route['callback'])) {
            return call_user_func_array($route['callback'], $params);
        }

        // Callback
        if(is_array($route['callback'])){
            $callback = $route['callback'];
        }else{
            $callback = explode(',', $route['callback']);
            $callback = array_map('trim', $callback);            
        }

        // Class
        $class = $callback[0];
        if (!class_exists($class)) {
             $class = trim($route['options']['controller'] . '\\' . $callback[0], '\\');
        }

        $class = str_replace(['.', '/', '//', '\\\\'],"\\" ,$class);
        if (!class_exists($class)) {
            $class = trim($this->controller_namespace . '\\' . $class);
        }

        // Method
        $method = $callback[1] ?? $this->getControllerDefaultMethod();

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
    
    /**
    * Execute fallback
    *
    */
    public function executeFallback($callback) {
        // Call function
        if (is_callable($callback)) {
          return call_user_func($callback);
      }

      // Callback
      if(is_array($callback)){
          $callback = $callback;
      }else{
          $callback = explode(',', $callback);
          $callback = array_map('trim', $callback);            
      }

      // Class
      $class = $callback[0];
      $class = str_replace(['.', '/', '//', '\\\\'],"\\" ,$class);
      if (!class_exists($class)) {
          $class = trim($this->controller_namespace . '\\' . $class);
      }

      // Method
      $method = $callback[1] ?? $this->getControllerDefaultMethod();

      // Call callback
      if (class_exists($class)) {
          $object = new $class;
          if (method_exists($object, $method)) {
              return call_user_func([$object, $method]);
          } else {
              throw new \Exception("The method " . $method . " is not exists at " . $class);
          }
      } else {
          throw new \Exception("Class " . $class . " is not found");
      }
    }

}