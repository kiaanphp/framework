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
namespace Kiaan\Security;

/*
|---------------------------------------------------
| Cross-Origin Resource Sharing
|---------------------------------------------------
*/
class Cors {
    
    /*
    * Global traits
    *
    */
    use \Kiaan\Application\Resources\Global\ExtendingTrait;
     
    /**
     *  Function CORS-compliant method. 
     *  It will allow any GET, POST, or OPTIONS requests from any origin.
     *
     *  In a production environment, you probably want to be more restrictive, but this gives you
     *  the general idea of what is involved.
     *
     */
    public function enable() {
        
        // Allow from any origin
        if(isset($_SERVER["HTTP_ORIGIN"]))
        {
            // You can decide if the origin in $_SERVER['HTTP_ORIGIN'] is something you want to allow, or as we do here, just allow all
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        }
        else
        {
            //No HTTP_ORIGIN set, so we allow any. You can disallow if needed here
            header("Access-Control-Allow-Origin: *");
        }

        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Max-Age: 86400");  // cache for 1 day

        if(isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "OPTIONS")
        {
            if (isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_METHOD"]))
                header("Access-Control-Allow-Methods: *");

            if (isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_HEADERS"]))
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

            //Just exit with 200 OK with the above headers for OPTIONS method
            exit(0);
        }
        
    }

}