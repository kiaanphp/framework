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
| Function
|---------------------------------------------------
*/
class Func {
    
    /*
    * Global traits
    *
    */
    use \Kiaan\Application\Resources\Global\ExtendingTrait;
     
    /**
     * Call
     * 
     * Call a callback with an array of parameters
     */
    public function call(callable $callback , $args=[]){
        return call_user_func_array($callback, $args);
    }

    /**
     * Static call
     * 
     * Call a static method and pass the arguments as array
     */
    public function staticCall(callable $callback , $args=[]){
        return forward_static_call_array($callback, $args);
    }

    /**
     * Argument
     * 
     * Return an item from the argument list
     */
    public function arg(int $num){
        return func_get_arg($num);
    }

    /**
     * Args
     * 
     * Returns an array comprising a function's argument list
     */
    public function args(){
        return func_get_args();
    }

    /**
     * Number Arguments
     * 
     * Returns the number of arguments passed to the function
     */
    public function numArgs(){
        return func_num_args();
    }

    /**
     * Exists
     * 
     * Return true if the given function has been defined
     */
    public function exists(string $function_name){
        return function_exists($function_name);
    }

    /**
     * All
     * 
     * Returns an array of all defined functions
     */
    public function all(){
        return get_defined_functions();
    }

}
