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
        if(isset($callback[1])){
            $method = $callback[1];
            $method__invoke = false;
        }else{
            $method = $this->getControllerDefaultMethod();
            $method__invoke = true;
        }

        // Call callback
        if (class_exists($class)) {
            $object = new $class;
            if (method_exists($object, $method)) {
                if($method__invoke && method_exists($object, '__invoke')) {
                    $result = call_user_func_array([$object, '__invoke'], $params);
                    if(is_string($result)){ echo $result; }
                    return $result;
                }else{
                    $result = call_user_func_array([$object, $method], $params);
                    if(is_string($result)){ echo $result; }
                    return $result;
                }
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
        if(isset($callback[1])){
            $method = $callback[1];
            $method__invoke = false;
        }else{
            $method = $this->getControllerDefaultMethod();
            $method__invoke = true;
        }

        // Call callback
        if (class_exists($class)) {
            $object = new $class;
            if (method_exists($object, $method)) {
                if($method__invoke && method_exists($object, '__invoke')) {
                    return call_user_func([$object, '__invoke']);
                }else{
                    return call_user_func([$object, $method]);
                }
            } else {
                throw new \Exception("The method " . $method . " is not exists at " . $class);
            }
        } else {
            throw new \Exception("Class " . $class . " is not found");
        }
    }

}