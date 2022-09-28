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
namespace Kiaan\Database\DB\Build;

/*
|---------------------------------------------------
| Uses
|---------------------------------------------------
*/
use Kiaan\Database\DB;

/*
|---------------------------------------------------
| Model
|---------------------------------------------------
*/
class Model extends DB {
    /*
    * Construct
    *
    */
    function __construct() {
        $this->pdo = self::$pdo_static;
    }

    /*
    * This
    *
    * Call as static
    */
    public static function this()
    {
        return new (get_called_class());
    }
}
