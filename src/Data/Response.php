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
namespace Kiaan\Data;

/*
|---------------------------------------------------
| Response
|---------------------------------------------------
*/
class Response {

    /**
     * Return json respoonse
     * 
     * Convert (array, object or text) to json
     * @params mixed $data
     * @return mixed
    */
    public function json($data) {
        return json_encode($data);
    }

    /**
     * Return array respoonse
     * 
     * @params mixed $data
     * @type (split, regex, str) $data
     * @params integer $num
     * @return mixed
    */
    public function array($data, $type=null, $num=null) {

    if(!is_array($data)){
    // Is json
    if (is_object(json_decode($data)))
    { 
        return json_decode($data, true);
    }
    // Is object
    elseif(is_object($data)){
        return (array) $data;
    }
    // Other
    else{
        //type: split, regex, str

        // null
        if($type==null){
            return (array) $data;
        }else{
        switch ($type) {
            case "split":
                if(empty($num)){$num = ',';}
                return explode($num, $data);

                break;
            case "regex":
                if(empty($num)){$num = '/[-\s:]/';}
                return preg_split($num, $data);

                break;
            case "str":
                if(empty($num)){$num = '1';}
                return str_split($data, $num);

                break;
            default:
                return (array) $data;
            }
        }

    }
    }else{
        return $data;
    }

    }

    /**
     * Return object respoonse
     *
     * @params mixed $data
     * @return mixed
    */
    public function object($data, $type=null, $num=null) {
        return (object) $this->array($data, $type, $num);
    }

    /**
     * Return text respoonse
     *
     * @params mixed $data
     * @type (split, regex, str) $data
     * @params integer $num
     * @return mixed
    */
    public function text($data, $value="", $type=null, $num=null) {
        if(is_array($data) || (is_object(json_decode($data))) || is_array($data)){
           $text = $this->array($data, $type, $num);
           return (string) implode($value, $text);

        }
        
         return (string) $data;
    }

    /**
     * Output data
     *
     * @param mixed $data
    */
    public function output($data) {
        if (! $data) {return ;}
        if (! is_string($data)) {
            $data = json_encode($data);
        }
        return $data;
    }

}