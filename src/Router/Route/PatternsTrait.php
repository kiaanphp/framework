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
    */
    protected function executePatterns($route, $values) {
        // Parameters
        preg_match_all('/{(.*?)}\//', $route['uri'], $params);
        $params = $params[1];
        
        foreach($params as $key => $value){
            $params = explode(":", $value)[0];
            $param_pattern = (isset(explode(":", $value)[1])) ? explode(":", $value)[1] : null;

            if(is_null($param_pattern)){
                $param_pattern = (array_key_exists($params, $this->patterns)) ? $this->patterns[$params] : $param_pattern;
            }

            switch ($param_pattern) {
                case 'alpha':
                    $param_pattern = "[A-Za-z]+";
                break;
                case 'alphaNumeric':
                    $param_pattern = "[A-Za-z0-9_]+";
                break;
                case 'numeric':
                    $param_pattern = "[0-9]+";
                break;
            }

            // Pattern
            $pattern = (isset($param_pattern)) ? '/'.$param_pattern.'/' : null;
            
            // False
            if(!is_null($pattern) && !preg_match($pattern, $values[$key])){ return false; }
        }

        // True
        return true;
    }

    /**
     * Pattern
     *
    */
    public function pattern($Parameter, $pattern) {
        $this->patterns[$Parameter] = $pattern;
    }

}