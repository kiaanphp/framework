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
    * Class
    *
    */
    protected static $__CLASS__ = __CLASS__; 

    /*
    * Call as static
    */
    public static function this()
    {
        return new static;
    }

    public static function model()
    {
        return new static;
    }

    public static function db()
    {
        return new static;
    }

    /*
    * Call as static
    *
    */
    public static function __callStatic(string $name, array $arguments)
    {
        $name = "_" . $name;
        return (new static::$__CLASS__)->$name(...$arguments);
    }

    /*
    * Call as non-static
    *
    */
    public function __call(string $name, array $arguments)
    {
        $name = "_" . $name;
        return (new static::$__CLASS__)->$name(...$arguments);
    }

}
