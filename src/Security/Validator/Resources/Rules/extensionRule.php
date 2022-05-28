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
class extensionRule {
    
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
    public function rule($name, $value, $parms) {

        if(is_array($value)){

            if(isset($value['tmp_name'])){

                if(is_file($value['tmp_name'])){

                    if(static::check($value, $parms)){
                        // True
                        return true;
                    }else{
                        // False
                        return false;
                    }
                    
                }else{
                    // False
                    return false;
                }

            }

        }else{

            if(static::check($value, $parms, false)){
                // True
                return true;
            }else{
                // False
                return false;
            }

        }

    }     

    public static function check($value, $parms, $tmp=true){

        $allowed = $parms;

        if($tmp==true){
            $filename = $value['name'];
        }else{
            $filename = $value;
        }

        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        if (!in_array($ext, $allowed)) {
            return false;
        }else{
            return true;
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
        return "'$name' is not support";
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