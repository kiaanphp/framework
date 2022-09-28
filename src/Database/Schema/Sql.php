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
namespace Kiaan\Database\Schema;

/*
|---------------------------------------------------
| Sql
|---------------------------------------------------
*/
class Sql {
    
    /*
    * drive
    */
    public $drive;

    /**
     * Generate sql string
    */
    public function generate()
    {
        $args = func_get_args();
        $name = $args[0];
        array_shift($args);

        return (new (__NAMESPACE__.'\\'.$this->drive))->{$name}(...$args);
    }

}