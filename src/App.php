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
use Kiaan\Application\App as Base;

/*
|---------------------------------------------------
| App
|---------------------------------------------------
*/
class App {
    
    /*
    * Facade
    *
    */
    use Facade;

    protected function __facade() {
        return \Kiaan\Application\App::class;
    }

}