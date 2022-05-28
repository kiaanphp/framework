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
use Kiaan\Directories\Folder as Base;

/*
|---------------------------------------------------
| Folder
|---------------------------------------------------
*/
class Folder {
     
    /*
    * Facade
    *
    */
    use Facade;

    protected function __facade() {
        return Base::class;
    }

}