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
namespace Kiaan\Http;

/*
|---------------------------------------------------
| Http
|---------------------------------------------------
*/
class Http {
    
    /*
    * Global traits
    *
    */
    use \Kiaan\Application\Resources\Global\ExtendingTrait;
     
    /*
    * Call Http
    *
    */
    protected function callHttp($method, $url, $data=[], $header=[]){
        // CURL init
        $curl = curl_init();

        // Method 
        $method = trim(strtoupper($method));

        switch ($method){
           case "POST":
              curl_setopt($curl, CURLOPT_POST, true);
              if ($data)
                 curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
              break;
           case "CUSTOM":
              curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
              if ($data)
                 curl_setopt($curl, CURLOPT_POSTFIELDS, $data);			 					
              break;
           default:
              if ($data)
                 $url = sprintf("%s?%s", $url, http_build_query($data));
        }

        // Handle cookies
        curl_setopt($curl, CURLOPT_COOKIEJAR, 'cookie.txt');
        curl_setopt($curl, CURLOPT_COOKIEFILE, '/var/www/ip4.x/file/tmp');

        // Header
        curl_setopt($curl, CURLOPT_ENCODING , "");
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);  
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

        // Execute
        $result = curl_exec($curl);
        if(!$result){return false;}
        curl_close($curl);
        
        return $result;
    }

    /*
    * Get
    *
    */
    public function get($url, $header=[]){
        // Call
        return $this->callHttp('GET', $url, array(), $header);
    }

    /*
    * Post
    *
    */
    public function post($url, $data=[], $header=[]){
        // Call
        return $this->callHttp('POST', $url, $data, $header);
    }

    /*
    * Method
    * 
    * Method custom
    */
    public function method($method, $url, $data=[], $header=[]){
        // Call
        return $this->callHttp($method, $url, $data, $header);
    }

}