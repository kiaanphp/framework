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
namespace Kiaan\Router\Route;

/*
|---------------------------------------------------
| Collect Trait
|---------------------------------------------------
*/
trait CollectTrait {

    /**
     * Add to collection (CRUD) routes
     *
     */
    protected function addToCollect($uri, $callback, $options, $list) {
        $methods = array_combine(array_column($list, "controller"), array_column($list, "method"));
        $methods_uri = array_combine(array_column($list, "controller"), array_column($list, "uri"));

        // Except
        if (isset($options['except'])) {
            $except = $options['except'];
            
            if (is_string($except)) {
                $except = explode(",", $options['except']);
                $except = array_map('trim', $except);
            }
            
            foreach($except as $key){                
                if (isset($methods[$key])) {
                    unset($methods[$key]);
                }
            }
            
        }

        // only
        if (isset($options['only'])) {
            $only = $options['only'];

            if (is_string($only)) {
                $only = explode(",", $options['only']);
                $only = array_map('trim', $only);
            }

            $list = array();
            foreach($only as $key){     

                if (isset($methods[$key])) {
                    $list = array_merge($list, [$key=>$methods[$key]]);
                }
            }

            $methods = $list;
        }

        // Add
        $name_cache = $options['name'] ?? null;

        foreach($methods as $key => $method){
            // Method
            if(empty($key)){
                $key = explode(',', $callback)[1] ?? null;
            }

            // Name
            if (isset($options['name']) && !empty($options['name'])) {
                $options['name'] = preg_replace('/\d/', 'a\\0b', $name_cache . '_' . $method);
            }

            // Callback
            if (is_string($callback)) {
                $callback_func = $callback . "," . $key;
            }

            // URI
            $method_uri = $uri.$methods_uri[$key];

            // Submit
            $this->addToMethods($method, $method_uri, $callback_func, $options);
        }

        return clone($this);
    }

    /**
     * Add to collection (Any) routes
     *
     */
    protected function addToAnyCollect($uri, $callback, $options, $methods) {
        // Except
        if (isset($options['except'])) {
            $except = $options['except'];
            
            if (is_string($except)) {
                $except = explode(",", $options['except']);
                $except = array_map('trim', $except);
            }

            $methods = array_flip($methods);

            foreach($except as $key){   
                if (isset($methods[$key])) {
                    unset($methods[$key]);
                }
            }

            $methods = array_flip($methods);
        }

        // only
        if (isset($options['only'])) {
            $only = $options['only'];

            if (is_string($only)) {
                $only = explode(",", $options['only']);
                $only = array_map('trim', $only);
            }

            $list = array();
            foreach($only as $key){                
                if (in_array($key, $methods)) {
                    array_push($list, $key);
                }
            }

            $methods = $list;
        }

        
        // Add
        $name_cache = $options['name'] ?? null;
        foreach($methods as $method){
            // Name
            if (isset($options['name']) && !empty($options['name'])) {
                $options['name'] = preg_replace('/\d/', 'a\\0b', $name_cache . '_' . $method);

            }
            // Submit
            $this->addToMethods($method, $uri, $callback, $options);
        }
        
        return clone($this);
    }

    /**
     * Add any collection routes
     *
     */
    public function any($uri, $callback, $options = []) {
        // Methods
        $methods = array("get","post","put","delete","patch","copy","options","lock","unlock","propfind");

        // Add to collection
        $this->addToAnyCollect($uri, $callback, $options, $methods);
    }

    /**
     * Methods collection routes
     *
     */
    public function methods($methods, $uri, $callback, $options = []) {
        // Methods
        $methods = array_unique($methods);

        // Add to collection
        $this->addToAnyCollect($uri, $callback, $options, $methods);
    }

    /**
     * Add crud collection routes
     *
     */
    public function crud($uri, $callback, $options = []) {
        // List
        $list = array(
            [
                "controller" => "index",
                "method" => "get",
                "uri" => "",
            ],

            [
                "controller" => "create",
                "method" => "get",
                "uri" => "/create",
            ],

            [
                "controller" => "store",
                "method" => "post",
                "uri" => "",
            ],

            [
                "controller" => "show",
                "method" => "get",
                "uri" => "/show//".$this->route_parameter[0]."id".$this->route_parameter[1],
            ],

            [
                "controller" => "edit",
                "method" => "get",
                "uri" => "/edit",
                "uri" => "/show//".$this->route_parameter[0]."id".$this->route_parameter[1]."/edit",
            ],

            [
                "controller" => "update",
                "method" => "put",
                "uri" => '/'.$this->route_parameter[0]."id".$this->route_parameter[1],
            ],

            [
                "controller" => "delete",
                "method" => "delete",
                "uri" => '/'.$this->route_parameter[0]."id".$this->route_parameter[1],
            ],
        );

        // Add to collection
        $this->addToCollect($uri, $callback, $options, $list);
    }

    /**
     * Add api crud collection routes
     *
     */
    public function apiCrud($uri, $callback, $options = []) {
        // List
        $list = array(
            [
                "controller" => "index",
                "method" => "get",
                "uri" => "",
            ],

            [
                "controller" => "store",
                "method" => "post",
                "uri" => "",
            ],

            [
                "controller" => "show",
                "method" => "get",
                "uri" => "/show//".$this->route_parameter[0]."id".$this->route_parameter[1],
            ],

            [
                "controller" => "update",
                "method" => "put",
                "uri" => '/'.$this->route_parameter[0]."id".$this->route_parameter[1],
            ],

            [
                "controller" => "delete",
                "method" => "delete",
                "uri" => '/'.$this->route_parameter[0]."id".$this->route_parameter[1],
            ],
        );

        // Add to collection
        $this->addToCollect($uri, $callback, $options, $list);
    }

    /**
     * Add cruds collection routes
     *
     */
    public function cruds(array $uri) {
        foreach($uri as $url){
            $this->crud($url[0], $url[1], []); 
        }
    }

    /**
     * Add api cruds collection routes
     *
     */
    public function apiCruds(array $uri) {
        foreach($uri as $url){
            $this->apiCrud($url[0], $url[1], []); 
        }
    }

}

