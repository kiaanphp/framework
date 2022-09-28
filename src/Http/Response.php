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
namespace Kiaan\Http;

/*
|---------------------------------------------------
| Response
|---------------------------------------------------
*/
class Response {
    
    /*
    * Global traits
    *
    */
    use \Kiaan\Application\Resources\Global\FilesystemPathTrait;
    use \Kiaan\Application\Resources\Global\ExtendingTrait;

    // validate only XML.
    protected function isValidXml($content)
    {
        if(!is_string($content)){
            return false;
        }

        $content = trim($content);
        
        if (empty($content)) {
            return false;
        }

        if (stripos($content, '<!DOCTYPE html>') !== false) {
            return false;
        }

        libxml_use_internal_errors(true);
        simplexml_load_string($content);
        $errors = libxml_get_errors();          
        libxml_clear_errors();  

        return empty($errors);
    }

    /*
    * Type of object
    */
    protected function type($data) {
        // XML
        if($this->isValidXml($data)) 
        { 
            return 'xml';
        }

        // Json
        if(is_string($data) && is_array(json_decode($data, true))) 
        { 
            return 'json';
        }

        // Array
        if(is_array($data)){
            return 'array';
        }

        // String
        if(is_string($data)){
            return 'string';
        }
    }

    /**
     * JSON Responses
     *
    */
    public function json($data, $response_code='200') {
        // Header
        header("Content-Type: application/json");

        // Response code
        http_response_code($response_code);

        // Conversion
        switch ($this->type($data)) {
            case "xml":
                $data = json_encode(simplexml_load_string($data));
               break;
            case "array":
                $data = json_encode($data);
               break;
            case "string":
                $data = json_encode($data);
                break;
        }

        // Return
        exit($data);
    }

    /**
     * XML Responses
     *
    */
    public function xml($data, $response_code='200') {
        // Header
        header("Content-type: text/xml; charset=utf-8");  

        // Response code
        http_response_code($response_code);

        // Conversion
        switch ($this->type($data)) {
            case "array":
                $xml = new \SimpleXMLElement('<root/>');
                array_walk_recursive($data, array ($xml, 'addChild'));
                $data =  $xml->asXML();
               break;
            case "json":
                $array = json_decode($data, true);
                $xml = new \SimpleXMLElement('<root/>');
                array_walk_recursive($array, array ($xml, 'addChild'));
                $data =  $xml->asXML();
               break;
        }

        // Return
        exit($data);
    }

    /**
     * Text Responses
     *
    */
    public function text($data, $response_code='200') {
        // Header
        header("Content-Type: text/plain; charset=utf-8");

        // Response code
        http_response_code($response_code);

        // Conversion
        switch ($this->type($data)) {
            case "array":
                $data = implode(", ", $data);
               break;
        }

        // Return
        exit($data);
    }    

    /**
     * Add header
     *
    */
    public function header($key, $value) {
        // Header
        header("$key: $value");

        return clone($this);
    }

    /**
     * Add headers by array
     *
    */
    public function headers(array $headers) {
        // Add headers
        foreach ($headers as $key => $value) {
            header("$key: $value");
        }

        return clone($this);
    }
    
    /**
     * Add HTTP response code
     *
    */
    public function code($response_code) {
        http_response_code($response_code);

        return clone($this);
    }

    /**
     * Stream file
     *
    */
    public function file($path) {      
        // open the file in a binary mode
        $name = $this->filesystemRoot() . $path;
        
        // If file not exists
        if(!file_exists($name)){
            return false;
        }

        $fp = fopen($name, 'rb');

        // send the right headers
        header("Content-Type: " . mime_content_type($name));
        header("Content-Length: " . filesize($name));

        // dump the picture and stop the script
        fpassthru($fp);
        exit;
    }
}