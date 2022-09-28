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
use Kiaan\Database\Auth as Base;

/*
|---------------------------------------------------
| Auth
|---------------------------------------------------
*/
class Auth {
    
    /*
    * Facade
    *
    */
    use Facade;

    protected function __facade() {
        return Base::class;
    }

}