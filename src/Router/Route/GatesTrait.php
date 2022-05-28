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
| Gates Trait
|---------------------------------------------------
*/
trait GatesTrait {
 
    /**
     * add to Gates
     *
     * @param string $gate
     * @param callback $callback
     */
    protected function addToGates(String $gate,Callable $callback) {
        // Gate
        $this->gate = strtolower($gate);

        // Callable
        if (is_callable($callback)) {
            call_user_func($callback);
        } else {
            throw new \Exception("Please provide valid callback function");
        }

        $this->gate = 'web';
    }

    /**
     * Set web group for routing
     *
     * @param callback $callback
     */
    public function web(Callable $callback) {
        $this->addToGates('web', $callback);
     }
 
     /**
      * Set api group for routing
      *
      * @param callback $callback
      */
     public function api(Callable $callback) {
        $this->addToGates('api', $callback);
     }

}