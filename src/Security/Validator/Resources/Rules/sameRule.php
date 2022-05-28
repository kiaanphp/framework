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
class sameRule {
    
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
        if(array_key_exists($parms[0], $this->request)){
            if($value == $this->request[$parms[0]]){
                return true;
            }else{
                return false;
            }
        }else{
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
        return "'$name' is not same other";
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