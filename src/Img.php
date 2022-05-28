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
use Kiaan\Media\Img as Base;

/*
|---------------------------------------------------
| Image
|---------------------------------------------------
*/
class Img {
    
    /*
    * Facade
    *
    */
    use Facade;

    protected function __facade() {
        return Base::class;
    }

}