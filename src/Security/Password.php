<?php

/**
 * Kiaan framework
 *
 * @author Hassan Kerdash kerdashhassan@gmail.com
 **/

/*
|---------------------------------------------------
| Namespace
|---------------------------------------------------
*/
namespace Kiaan\Security;

/*
|---------------------------------------------------
| Password
|---------------------------------------------------
*/
class Password {
    
    /*
    * Global traits
    *
    */
    use \Kiaan\Application\Resources\Global\ExtendingTrait;
     
    /**
     * Encrypt password
     * 
    */
    public function encrypt($pass)
    {
        return password_hash($pass, PASSWORD_DEFAULT);
    }

    /**
     * Verify password
     * 
    */
    public function verify($pass, $hash)
    {
        return password_verify($pass, $hash);
    }

    /**
     * Generate password
     * 
    */
    public function generate($length=14, $container=["alphaSmall"=>true, "alphaCaps"=>true, "numeric"=>true, "special"=>true])
    {
        // small letters
        $_alphaSmall = ("alphaSmall") ? 'abcdefghijklmnopqrstuvwxyz' : '';
        // CAPITAL LETTERS  
        $_alphaCaps  = ("alphaCaps") ? strtoupper($_alphaSmall) : '';         
        // numerics       
        $_numerics   = ("numeric") ? '1234567890' : '';                   
        // Special Characters         
        $_specialChars = ("special") ? '`~!@#$%^&*()-_=+]}[{;:,<.>/?\'"\|' : '';   

        // Contains all characters
        $_container = $_alphaSmall.$_alphaCaps.$_numerics.$_specialChars;

        // will contain the desired pass
        $password = '';    

        // Loop till the length mentioned
        for($i = 0; $i < $length; $i++) {  
            // Get Randomized Length                               
            $_rand = rand(0, strlen($_container) - 1);   
            // returns part of the string [high tensile strength]                
            $password .= substr($_container, $_rand, 1);                
        }

        // Returns the generated Pass
        return $password;       
    }
    
}