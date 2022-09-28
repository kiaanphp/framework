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
| Request
|---------------------------------------------------
*/
class Request {
    
    /*
    * Global traits
    *
    */
    use \Kiaan\Application\Resources\Global\ExtendingTrait;

    /**
     * Get uri
     *
     * @return string
    */
    public function uri() {
        $SCRIPT_NAME = $_SERVER['SCRIPT_NAME'];
        $folder = explode("/",$SCRIPT_NAME)[1];
        $folder = ($folder == 'index.php') ? "" : $folder;
        
        $script_name = preg_replace('~/+~', '/', dirname($_SERVER['SCRIPT_NAME']), 1);

        $request_uri = urldecode($_SERVER['REQUEST_URI']);
        $request_uri = rtrim($request_uri, '/');
        $request_uri = str_replace($script_name,'', $request_uri);

        $path_name = parse_url($request_uri)['path'];
        $path_name = str_replace('//', '/', $path_name);
        $path_name = preg_replace('~/+~', '/', $path_name);
        
        return $path_name ?: '/';
     }

    /**
     * Get protocol
     *
     * @return string
     */
    public function protocol() {
        $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        return $protocol;
    }

    /**
     * Get server
     *
     * @return string
     */
    public function server() {
        return $_SERVER['SERVER_NAME'];
    }

    /**
     * Get status
     *
     * @return string
     */
    public function status() {
        return $_SERVER['REDIRECT_STATUS'];
    }

    /**
     * Get port
     *
     * @return string
     */
    public function port() {
        return $_SERVER['SERVER_PORT'];
    }

    /**
     * Get method
     *
     * @return string
     */
    public function method() {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Get request time
     *
     * @return string
     */
    public function time() {
        return $_SERVER['REQUEST_TIME'];
    }

    /**
     * Get query
     *
     * @return string
     */
    public function query() {
        $script_name = str_replace('\\', '', dirname($_SERVER['SCRIPT_NAME']));
        $request_uri = urldecode($_SERVER['REQUEST_URI']);
        $request_uri = rtrim(preg_replace("#^" . $script_name. '#', '', $request_uri), '/');
        $path_name = parse_url($request_uri)['path'];
        $request_uri = rtrim(preg_replace("#^" . $path_name. '#', '', $request_uri), '/');

        $query = $request_uri;
        return $query;
    }

    /*
    * Get operating-system
    *
    */
    public function os(){
        return strtolower($_SERVER['HTTP_SEC_CH_UA_PLATFORM']);
    }

    /*
    * Browser
    *
    */
    function browser() {
        try {
            $browser = explode(',', $_SERVER['HTTP_SEC_CH_UA'])[2];
            preg_match('/"([^"]+)"/', $browser, $browser_name);
            $browser_name = $browser_name[1];
        }catch (\Throwable $th) {
            $browser_name = 'unknown';
        }

        return get_browser();
    }

    /**
     * Get url
     *
     */
    public function url() {
        $protocol = ($_SERVER['REQUEST_SCHEME'] ?? 'http') . '://';
        $host = $_SERVER['HTTP_HOST'] ?? null;
        
        $script_name = str_replace('\\', '', dirname($_SERVER['SCRIPT_NAME']));
        $script_name = $script_name . '/' . trim($this->uri(), '/') . $this->query();
        
        return trim($protocol . $host . $script_name, '/');
    }

}