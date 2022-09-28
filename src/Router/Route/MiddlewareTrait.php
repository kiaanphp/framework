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
| Middleware Trait
|---------------------------------------------------
*/
trait MiddlewareTrait {

    /**
     * Execute middleware
     * 
     */
    public function executeMiddleware($route) {
        // Middlewares
        $middlewares = $route['options']['middleware'];
        
        //Check of middlewares
        foreach ($middlewares as $key => $middleware) {
            // Call function
            if (is_callable($middleware)) {
                call_user_func($middleware);
                continue;
            }

            // Class
            $class = trim(explode(',', $middleware)[0]) ?? null;
            $class = trim(explode(':', $class)[0]) ?? null;

            // Method
            $method = explode(':', $middleware)[1] ?? $this->getMiddlewareMethod();
            $method = trim(explode(',', $method)[0]) ?? $this->getMiddlewareMethod();

            // Parameters
            $params = explode(',', $middleware) ?? null;
            array_shift($params);
            $params = array_map('trim', $params) ?? null;    
            $params = array_filter($params);

            // Middleware namespace
            if (!class_exists($class)) {
                $class = trim($this->getMiddlewareNamespace() . '\\' . $class);
             }


            // Call callback
            if (class_exists($class)) {
                $object = new $class;
                if (method_exists($object, $method)) {
                    call_user_func_array([$object, $method], $params);
                } else {
                    throw new \Exception("The method " . $method . " is not exists at " . $class);
                }
            } else {
                throw new \Exception("Class " . $class . " is not found");
            }

        }
    }

}