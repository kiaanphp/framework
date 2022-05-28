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
class sizeRule {
    
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
        $valid = true;

        switch($parms[0]) {
            case '==':
            $valid = $value["size"] == $parms[1];
            break;

            case '!=':
            $valid = $value["size"] != $parms[1];
            break;

            case '<':
            $valid = $value["size"] < $parms[1];
            break;
    
            case '>':
            $valid = $value["size"] > $parms[1];
            break;

            case '<=':
            $valid = $value["size"] <= $parms[1];
            break;

            case '>=':
            $valid = $value["size"] >= $parms[1];
            break;

            default:
            $valid = $value["size"] == $parms[1];
            break;
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
        return "'$name' size not valid.";
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