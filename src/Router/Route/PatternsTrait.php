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
| Patterns Trait
|---------------------------------------------------
*/
trait PatternsTrait {

    /**
     * Execute patterns
     * 
     *
    */
    public function executePatterns($route, $values) {
        
        // Parameters
        preg_match_all('/{(.*?)}\//', $route['uri'], $params);
        $params = $params[1];
        
        foreach($params as $key => $value){
            $params = explode(":", $value)[0];
            $pattern = (isset(explode(":", $value)[1])) ? '/'.explode(":", $value)[1].'/' : null;
            
            // False
            if(!is_null($pattern) && !preg_match($pattern, $values[$key])){ return false; }
        }

        // True
        return true;
    }

}