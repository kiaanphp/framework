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
class imageRule {
    
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

                    if($this->check($value, $parms)){
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

            if($this->check($value, $parms, false)){
                // True
                return true;
            }else{
                // False
                return false;
            }

        }

    }    

    public function check($value, $parms, $tmp=true){
        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        if($tmp==true){
            $mimetype = finfo_file($finfo, $value['tmp_name']);
        }else{
            $mimetype = finfo_file($finfo, $value);
        }

        foreach ($parms as $parm) {
            if ($mimetype == 'image/jpg' 
            || $mimetype == 'image/jpeg' 
            || $mimetype == 'image/gif' 
            || $mimetype == 'image/png'
            || $mimetype == 'image/bmp'
            || $mimetype == 'image/vnd.microsoft.icon'
            || $mimetype == 'image/tiff'
            || $mimetype == 'image/x-icon'
            || $mimetype == 'image/vnd.wap.wbmp'
            || $mimetype == 'image/webp'
            || $mimetype == 'image/x-jng'
            || $mimetype == 'image/svg+xml'
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