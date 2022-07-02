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
| Csrf Trait
|---------------------------------------------------
*/
trait CsrfTrait {

    /**
     * Get cross-site request forgery 
     * 
    */
    public function getCsrf() {
        return $this->csrf;
    }

    /**
     * Set cross-site request forgery 
     * 
    */
    public function setCsrf(array $value) {
        return $this->csrf = $value;
    }
    
    /**
     * Execute cross-site request forgery 
     * 
     * 
    */
    protected function executeCsrf($route) {
        if($route['gate']=='web' && $route['method']!='get' && $this->csrf['enable']){
            
            if(isset($_POST[$this->csrf['input']])){
                if($_POST[$this->csrf['input']] != $this->csrf['value']){
                    // 403
                    http_response_code(403);
                    throw new \Exception("CSRF not vaild.");
                }
            }else{
                // 403
                http_response_code(403);
                throw new \Exception("CSRF not vaild.");
            }
            
        }
    }

}