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
namespace Kiaan;

/*
|---------------------------------------------------
| Uses
|---------------------------------------------------
*/
use Kiaan\Facade;
use Kiaan\Dev\Middleware as Base;

/*
|---------------------------------------------------
| Middleware
|---------------------------------------------------
*/
class Middleware {
    
    /*
    * Facade
    *
    */
    use Facade;

    protected function __facade() {
        return Base::class;
    }

}