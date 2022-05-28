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
use Kiaan\Dev\Variable as Base;

/*
|---------------------------------------------------
| Variable
|---------------------------------------------------
*/
class Variable {
    
    /*
    * Facade
    *
    */
    use Facade;

    protected function __facade() {
        return Base::class;
    }

}