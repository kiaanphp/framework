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
| Application
|---------------------------------------------------
*/
class Application implements Interfaces\Application {
   
    /**
     * Get name of application folder
     *
     * @return string
     */
    public function folder() {
        $SCRIPT_NAME = $_SERVER['SCRIPT_NAME'];
        $folder = explode("/",$SCRIPT_NAME)[1];
        $folder = ($folder == $_SERVER['SCRIPT_FILE_NAME']) ? "" : $folder;
        
        return $folder ?? null;
    }
    
}