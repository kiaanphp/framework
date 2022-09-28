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
namespace Kiaan\Router;

/*
|---------------------------------------------------
| Route
|---------------------------------------------------
*/
class Route {
    
    /**
    * Traits
    *
    */
    use Route\GatesTrait;
    use Route\MethodsTrait;
    use Route\ControllerTrait;
    use Route\MiddlewareTrait;
    use Route\CsrfTrait;
    use Route\PatternsTrait;
    use Route\OptionsTrait;
    use Route\ToolsTrait;
    use Route\CollectTrait;

    /*
    * Global traits
    *
    */
    use \Kiaan\Application\Resources\Global\ExtendingTrait;
    
    /**
    * Route containers
    *
    */
    protected $routes = [];

    /**
    * Gate
    *
    */
    protected $gate = 'web';

    /**
    * Prefix gates
    *
    */
    protected $prefixGates = ["web" => '', "api" => "api"];

    /**
    * Controller namespace
    *
    */
    protected $controller_namespace;

    /**
    * Controller default method
    *
    */
    protected $controllerDefaultMethod;

    /**
    * Middleware namespace
    *
    */
    protected $middleware_namespace;

    /**
    * Middleware method
    *
    */
    protected $middlewareMethod;

    /**
    * Method input
    *
    */
    protected $methodInput = '_method';

    /**
    * Patterns
    *
    */
    protected $patterns = [];

    /**
    * Current
    *
    */
    protected $current;

    /**
    * Options
    *
    */
    protected $options = [
        "prefix" => null,
        "suffix" => null,
        "name" => null,
        "controller" => null,
        "middleware" => array(),
    ];

    /**
    * Method input
    *
    */
    protected $csrf = ["enable"=>true, "value"=>null, "input"=>"_csrf"];

    /**
    * Route parameter
    *
    */
    protected $route_parameter = ["{", "}"];

    /**
    * Route optional parameter
    *
    */
    protected $route_optional_parameter = ["[", "]"];

    /**
    * Fallback
    *
    */
    protected $fallback = null;

    /**
    * Route
    *
    */
    protected $route = null;

    /**
    * Routes in group
    *
    */
    protected $routes_in_group = false;

    /**
    * Routes not in group toggle
    *
    */
    protected $routes_not_in_group_toggle = true;

    /**
     * Construct
     * 
    */
    public function __construct(){}

    /**
     * Get prefix gates
     * 
    */
    public function getPrefixGates() {
        return $this->prefixGates;
    }

    /**
     * Set prefix gates
     * 
    */
    public function setPrefixGates($gate, $prefix) {
        $this->prefixGates[$gate] = $prefix;
        return clone($this);
    }

    /**
     * Get method input
     * 
    */
    public function getMethodInput() {
        return $this->methodInput;
    }

    /**
     * Set method input
     * 
    */
    public function setMethodInput($value) {
        $this->methodInput = $value;
        return clone($this);
    }
    
    /**
     * Get controller namespace
     * 
    */
    public function getControllerNamespace() {
        return $this->controller_namespace;
    }

    /**
     * Set controller namespace
     * 
    */
    public function setControllerNamespace($value) {
        $this->controller_namespace = $value;
        return clone($this);
    }

    /**
     * Get controller default method
     * 
    */
    public function getControllerDefaultMethod() {
        return $this->controllerDefaultMethod;
    }

    /**
     * Set controller default method
     * 
    */
    public function setControllerDefaultMethod($value) {
        $this->controllerDefaultMethod = $value;
        return clone($this);
    }

    /**
     * Get middleware namespace
     * 
    */
    public function getMiddlewareNamespace() {
        return $this->middleware_namespace;
    }

    /**
     * Set middleware namespace
     * 
    */
    public function setMiddlewareNamespace($value) {
        $this->middleware_namespace = $value;
        return clone($this);
    }

    /**
     * Get middleware method
     * 
    */
    public function getMiddlewareMethod() {
        return $this->middlewareMethod;
    }

    /**
     * Set middleware method
     * 
    */
    public function setMiddlewareMethod($value) {
        $this->middlewareMethod = $value;
        return clone($this);
    }

    /**
     * Set options defaults
     * 
    */
    protected function setOptionsDefaults(){
        $this->options = [
            "prefix" => null,
            "suffix" => null,
            "name" => null,
            "controller" => null,
            "middleware" => array(),
        ];
    }

    /**
     * Add route
     *
     * @param String methods
     * @param String $uri
     * @param Callable|String $callback
     */
    protected function add(String $method, String $uri, $callback, Array $options = []) {
        // URI
        $uri = trim($this->options['prefix'] . $uri . '/' . $this->options['suffix'], '/');
        
        $uri = $this->prefixGates[$this->gate].'/'.$uri;
        $uri = ($uri != '/') ? '/'.$uri : $uri;
        $uri = str_replace("//", "/", $uri);
        $uri = ($uri == '/') ? $uri : rtrim($uri, '/');

        // Status
        $status_route = true;
        $status_route_index = array_search($uri, array_column($this->routes, 'uri'));

        if(is_numeric($status_route_index)){
            $status_route_selected = $this->routes[$status_route_index];

            $status_route = (
            $status_route_selected['gate'] == $this->gate
            && $status_route_selected['method'] == $method
            ) ? false : true ;
        }

        // Add
        if($status_route === true) {
            // Route
            $route = [
                "gate" => $this->gate,
                "method" => $method,
                "uri" => $uri,
                "callback" => $callback,
                "options" => [
                    "name" => $this->options['name'],
                    "controller" => $this->options['controller'],
                    "middleware" => $this->options['middleware'],
                ],
            ];
            
            // Set options defaults
            $this->setOptionsDefaults();

            // Add to routes
            $this->routes[] = $route;
        }

        return clone($this);
    }

    /**
     * Run
     * 
    */
    public function run()
    {
        // Script file name
        $script_filename = explode("/", $_SERVER['SCRIPT_FILENAME']);
        $script_filename = $script_filename[count($script_filename)-1];

        // Script folder name
        $script_foldername = substr($_SERVER['SCRIPT_NAME'], 0, strpos($_SERVER['SCRIPT_NAME'], basename($_SERVER['SCRIPT_NAME'])));
        
        // Script folder
        $script_folder = explode("/", trim($script_foldername, "/"));
        $script_folder = $script_folder[count($script_folder)-1];

        // Routes
        $routes = $this->routes;
        
        // QUERY_STRING
        if(!isset($_SERVER['QUERY_STRING'])){ $_SERVER['QUERY_STRING']=''; };

        // Request
        $request = str_replace('%20', ' ', $_SERVER['REQUEST_URI']);
        $request = rtrim($request, '?'.$_SERVER['QUERY_STRING']);
        $request = '/' . $script_folder . '/' . substr($request, strlen($script_foldername)) . '/';

        $request = ltrim($request, "/");
        $request_url = ltrim($request, "/");
        
        $request = ltrim($request, $script_folder);
        $request = (empty($request)) ? $request_url : $request ;
        
        $request = '/' . ltrim($request, '/');
        // $request = ($request == '/') ? '/' : urldecode($request) . '/';
        $request = rtrim($request, '/');
        $request = (empty($request)) ? '/' : $request . '/';

        // Method
        $method = strtolower($_SERVER["REQUEST_METHOD"]);

        // Custom method
        if($method == 'post'){
            if (isset($_POST[$this->methodInput])) {
                $method  = strtolower($_POST[$this->methodInput]);
            }
        }

        // Get route & check method
        $list = array_values(array_column($routes, 'method'));
        $keys = array_keys(array_filter($list, function($value) use ($method) {return $value == $method;}));
        $routes = array_intersect_key($routes, array_flip($keys));


        // Prepare uri of routes && check
        foreach ($routes as $key => $value) {
            $routes[$key]['uri'] = ($routes[$key]['uri'] == '/') ? '/' : $routes[$key]['uri'] . '/';
            $myRoute = $routes[$key];
            $routes[$key]['uri'] = '#^' . preg_replace("/\/" . $this->route_parameter[0] . "(.*?)" . $this->route_parameter[1] . "/", '\/([^\/]*)', $routes[$key]['uri']) . '$#';

            if (preg_match($routes[$key]['uri'], $request, $params)) {
                // Route
                $route = $myRoute;
                
                // Parameters
                array_shift($params);
                
                // Patterns
                if (!$this->executePatterns($route, $params)) { continue; }

                // Middleware
                $this->executeMiddleware($route);

                // Cross-site request forgery 
                $this->executeCsrf($route);

                // Current
                $this->current = $route;

                // Controller
                return $this->executeController($route, $params);
            }

        }
        
        // Fallback
        if(!is_null($this->fallback)){
            return $this->executeFallback($this->fallback);
        }

        // 404
        http_response_code(404);
        throw new \Exception("Routes not found!");
    }

}