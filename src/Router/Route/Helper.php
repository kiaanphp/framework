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

use Exception;

/*
|---------------------------------------------------
| Helper
|---------------------------------------------------
*/
trait Helper {

  /**
   * Calls
   * 
   * @param string $name
   * @param array $arguments
   *
   * @return mixed
   */
  public function __call(string $name, array $arguments)
  {
    if(key_exists($name, $this->helpers)){
      return call_user_func_array($this->helpers[$name], $arguments);
    }else{
      throw new Exception("'$name' method is not found.");
    }
  }

  /**
   * Add helper
   * 
   * @param string $name
   * @param callable $function
   * @return void
  */
  public function addHelper(string $name, callable $function) {
    $this->helpers[$name] = $function;
  }

}