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
namespace Kiaan\Database\Auth;

/*
|---------------------------------------------------
| Uses
|---------------------------------------------------
*/
use PDO;

/*
|---------------------------------------------------
| Helpers
|---------------------------------------------------
*/
trait Helpers {
    
    /**
     * Generate JWT code
    *
    */
    protected function generate_jwt_code($id, $expire) {
        // Generate token
        $secret = $this->generate_token_jwt();
        $headers = array('alg'=>'HS256','typ'=>'JWT');
        $payload = array('sub'=>'1234567890','id'=>$id, 'secret'=>$secret, 'exp'=>(time() + $expire));
        $token = $this->generate_jwt($headers, $payload, $secret);
        
        // Update
        $this->pdo->exec("UPDATE {$this->table} SET {$this->token_field}='{$secret}', {$this->jwt_field}='{$token}' WHERE {$this->primary_field}='{$id}' LIMIT 1");

        // Return
        return $token;
    }

    /**
     * Generate JWT
    *
    */
    protected function generate_jwt($headers, $payload, $secret = 'secret') {
        $headers_encoded = rtrim(strtr(base64_encode(json_encode($headers)), '+/', '-_'), '=');
        
        $payload_encoded = rtrim(strtr(base64_encode(json_encode($payload)), '+/', '-_'), '=');
        
        $signature = hash_hmac('SHA256', "$headers_encoded.$payload_encoded", $secret, true);
        $signature_encoded = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');
        
        $jwt = "$headers_encoded.$payload_encoded.$signature_encoded";
        
        return $jwt;
    }

    /**
     * Generate Token Jwt
     * 
    */
    protected function generate_token_jwt($length=14, $container=["alphaSmall"=>true, "alphaCaps"=>true, "numeric"=>true])
    {
        // small letters
        $_alphaSmall = ("alphaSmall") ? 'abcdefghijklmnopqrstuvwxyz' : '';
        $_alphaCaps  = ("alphaCaps") ? strtoupper($_alphaSmall) : '';         
        $_numerics   = ("numeric") ? '1234567890' : '';                   
        $_container = $_alphaSmall.$_alphaCaps.$_numerics;
       
        $token = '';    
        for($i = 0; $i < $length; $i++) {  
            $_rand = rand(0, strlen($_container) - 1);   
            $token .= substr($_container, $_rand, 1);                
        }

        // Returns
        return $token;       
    }
    
}