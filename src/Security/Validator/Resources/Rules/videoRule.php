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
class videoRule {
    
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
        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        if($tmp==true){
            $mimetype = finfo_file($finfo, $value['tmp_name']);
        }else{
            $mimetype = finfo_file($finfo, $value);
        }

        foreach ($parms as $parm) {
            if ($mimetype == 'video/3gpp' 
            || $mimetype == 'video/mp4' 
            || $mimetype == 'video/mpeg' 
            || $mimetype == 'video/ogg' 
            || $mimetype == 'video/quicktime' 
            || $mimetype == 'video/webm' 
            || $mimetype == 'video/x-flv'
            || $mimetype == 'video/x-mng'
            || $mimetype == 'video/x-ms-asf'
            || $mimetype == 'video/x-ms-wmv'
            || $mimetype == 'video/x-msvideo'
            ) {
                return true;
            }
        }

        return false;
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