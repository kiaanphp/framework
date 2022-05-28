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
namespace Kiaan\Security\Validator\Resources\Rules;

/*
|---------------------------------------------------
| Class
|---------------------------------------------------
*/
class ipv4Rule {
    
    /**
     * handle
     * 
    **/
    public function handle()
    {
        return [
            "rule" => "rule",
            "message" => "message",
            "text" => "text"
        ];
    }    

    /**
     * Rule
     * 
     * $name string
     * $value string
     * $parms array
     * @return bool
     *  
    **/
    public function rule($name, $value, $parms)
    {
        // Check
        if(filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) && !is_null($value) && !empty($value)){
            // True
            return true;
        }else{
            // False
            return false;
        }
    }    

    /**
     * Message
     * 
     * $name string
     * $value string
     * $parms array
     * @return string
     *  
    **/
    public function message($name, $value, $parms){
        return "'$name' is not IP";
    }

    /**
     * Text 
     * 
     * $name string
     * $value string
     * $parms array
     * $message string
     * @return string
     *  
    **/
    public function text($name, $value, $parms, $message){
        return $message;
    }

}