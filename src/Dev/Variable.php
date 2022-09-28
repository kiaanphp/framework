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
namespace Kiaan\Dev;

/*
|---------------------------------------------------
| Variable
|---------------------------------------------------
*/
class Variable {
    
    /*
    * Global traits
    *
    */
    use \Kiaan\Application\Resources\Global\ExtendingTrait;
     
    /**
     * Get the type of a variable or set type("boolean" or "bool" | "integer" or "int" | "float" or "double" | "string" | "array" | "object" | "null");
     */
    public function type($var, $value=""){
        if(empty($value)){
            // get
            return gettype($var);
        }else {
            // set
            settype($var, $value);
        }
    }
    
    /**
     * Displays a variable and arrays or other format
     */
    public function dump($data){
        $html = '<pre style="margin-bottom: 18px;
        border: 1px solid #e1e1e8;
        border-radius: 4px;
        -moz-border-radius: 4px;
        -webkit-border radius: 4px;
        display: block;
        font-size: 14px;
        white-space: pre-wrap;
        word-wrap: break-word;
        border-radius: 3px; font-weight: bold; 
        padding: 20px; 
        background: #263238; 
        color: #ECEFF1; 
        box-shadow: 5px 10px inset;
        font-family: Menlo,Monaco,Consolas,\'Courier New\',monospace;">';
        
        echo $html;

        if (is_string($data)) {
            echo $data;
        } else {
            print_r($data);
        }
        echo "</pre>";
    }
  
    /**
     * Dump and die
    */
    public function dd($data){
        $this->dump($data);
        die();
    }

}
