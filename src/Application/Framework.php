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
namespace Kiaan\Application;

/*
|---------------------------------------------------
| Uses
|---------------------------------------------------
*/
use Kiaan\Application\App;

/*
|---------------------------------------------------
| App
|---------------------------------------------------
*/
class Framework implements Interfaces\Framework {

    /*
    * Singleton pattern
    */
    protected function __construct() {}
    

    /*
    * Run helpers
    */
    public static function runHelpers()
    {
        return include((new App)->helpers());
    }

    /*
    * Run framework
    */
    public static function run()
    {
        App::run();
    }

}