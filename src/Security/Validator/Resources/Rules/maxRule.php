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
class maxRule {
    
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
        $valid = false;
        
        if (is_array($parms) || is_numeric($parms[0]) || is_string($parms[0])) {
            $valid = is_array($value) ? count($value) <= $parms[0] : $valid;
            $valid = is_numeric($value) ? $value <= $parms[0] : $valid;
            $valid = is_string($value) ? strlen($value) <= $parms[0] : $valid;
            $valid = is_string($value) && is_file($value) ? filesize($value)/1024 <= $parms[0] : $valid;
        }

        if($valid){
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
        return "'$name' is bigger than required";
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