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
namespace Kiaan\Router\Route;

/*
|---------------------------------------------------
| Tools Trait
|---------------------------------------------------
*/
trait ToolsTrait {

    /**
    * Redirect
     *
    */
    public function redirect($new, $old) {
      // List
      $list = array_column($this->routes, 'uri');
      
      // New
      $new = trim($new);
      $new = '/'.trim($new, '/');

      // Old
      $old = trim($old);
      $old = '/'.trim($old, '/');

      // Match
      if (in_array($old, $list))
      {
        // Route
        $route = $this->routes[array_search($old, $list)];

        // New route
        $new_route = $route;
        $new_route['uri'] = $new;

        // Add to routes
        array_push($this->routes, $new_route);
      }
      else
      {
        throw new \Exception("Route not found!");
      }

      return clone($this);
    }

    /**
    * Get list of routes
     *
    */
    public function list() {
      $routes = array();

      foreach($this->routes as $route){
        $routes[] = [
          "gate" => $route["gate"],
          "method" => $route["method"],
          "name" => $route["options"]['name'],
          "uri" => $route["uri"],
          "url" => $this->link($route["uri"])
        ];
      }
      
      return json_decode(json_encode($routes));
    }

    /**
    * Get url route by name
    *
    */
    public function url($name, $parameters=[]) {
        // List
        $list = array_filter(array_column(array_column($this->routes, 'options'), 'name'));
        
        // Get key
        $key = array_search($name, $list);

        // False
        if(!isset($this->routes[$key])){
            return false;
        }

        // WWW
        $path = trim($this->routes[$key]['uri'],'/');

        // Parameters
        $keys = array_keys($parameters);
        $keysWithParameters = array_map(function($x){ return '{' . $x . '}'; }, $keys);
        $keysWithOptionalParameters = array_map(function($x){ return '[' . $x . ']'; }, $keys);
        $keys = array_merge($keysWithParameters, $keysWithOptionalParameters);

        $values = array_values($parameters);
        $values = array_merge($values, $values);

        // Path
        $path = str_replace($keys, $values, $path);

        // Protocol
        $protocol = ($_SERVER['REQUEST_SCHEME'] ?? 'http') . '://';

        // Host
        $host = $_SERVER['HTTP_HOST'] ?? null;
        
        // Script name
        $script_name = str_replace('\\', '', dirname($_SERVER['SCRIPT_NAME']));
        $script_name = str_replace('/public', '', $script_name).'/'.$path;

        // URL
        $url = $protocol . $host . $script_name;

        return $url;
    }

    /**
     * go
     * 
     */
    public function go($name, $parameters = []) {
      header('location: ' . $this->url($name, $parameters));
      exit();
    }

    /**
     * Get url
     *
     * @param string $path
     * @return string $path
     */
    protected function link($path='') {
      $protocol = ($_SERVER['REQUEST_SCHEME'] ?? 'http') . '://';
      $host = $_SERVER['HTTP_HOST'] ?? null;
      
      $script_name = str_replace('\\', '', dirname($_SERVER['SCRIPT_NAME']));
      $script_name = $script_name . '/' . trim($path, '/');
      
      return trim($protocol . $host . $script_name, '/');
  }

    /**
    * Current
    *
    */
    public function current() {
      return (object) [
        "gate" => $this->current["gate"],
        "method" => $this->current["method"],
        "name" => $this->current["options"]['name'],
        "uri" => $this->current["uri"],
        "url" => $this->link($this->current["uri"])
      ];
    }

    /**
    * Fallback
    *
    */
    public function fallback($callback) {
      $this->fallback = $callback;
    }

}