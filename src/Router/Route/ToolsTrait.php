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
namespace Kiaan\Router\Route;

/*
|---------------------------------------------------
| Tools Trait
|---------------------------------------------------
*/
trait ToolsTrait {

    /**
    * Get list of routes
     *
    */
    public function list() {
      return $this->routes;
    }

    /**
    * Get url route by name
    *
    */
    public function url($name) {
        // List
        $list = array_filter(array_column(array_column($this->routes, 'options'), 'name'));
        
        // Get key
        $key = array_search($name, $list);

        // False
        if($key==''){
            return false;
        }

        // WWW
        $path = trim($this->routes[$key]['uri'],'/');
        $protocol = ($_SERVER['REQUEST_SCHEME'] ?? 'http') . '://';
        $host = $_SERVER['HTTP_HOST'] ?? null;
        
        $script_name = str_replace('\\', '', dirname($_SERVER['SCRIPT_NAME']));
        $script_name = str_replace('/public', '', $script_name).'/'.$path;

        // URL
        $url = $protocol . $host . $script_name;

        return $url;
    }

}