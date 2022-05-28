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

     /**
     * Request constructor
     *
     * @return void
     */
      public function __construct(){}

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
        
        return $path_name?:'/';
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

    /**
     * Get content from url
     *
     * @return string
     */
    public function content($url) {
        return file_get_contents($url);
    }

    /*
    * Get operating-system
    *
    */
    public function os(){

        $user_agent = $_SERVER['HTTP_USER_AGENT'];

        $os_platform = "Unknown OS Platform";
    
        $os_array = array(
            '/windows NT 10.0/i'    =>  'Windows 10',
            '/windows nt 6.2/i'     =>  'Windows 8',
            '/windows nt 6.1/i'     =>  'Windows 7',
            '/windows nt 6.0/i'     =>  'Windows Vista',
            '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
            '/windows nt 5.1/i'     =>  'Windows XP',
            '/windows xp/i'         =>  'Windows XP',
            '/windows nt 5.0/i'     =>  'Windows 2000',
            '/windows me/i'         =>  'Windows ME',
            '/win98/i'              =>  'Windows 98',
            '/win95/i'              =>  'Windows 95',
            '/win16/i'              =>  'Windows 3.11',
            '/macintosh|mac os x/i' =>  'Mac OS X',
            '/mac_powerpc/i'        =>  'Mac OS 9',
            '/linux/i'              =>  'Linux',
            '/ubuntu/i'             =>  'Ubuntu',
            '/iphone/i'             =>  'iPhone',
            '/ipod/i'               =>  'iPod',
            '/ipad/i'               =>  'iPad',
            '/android/i'            =>  'Android',
            '/blackberry/i'         =>  'BlackBerry',
            '/webos/i'              =>  'Mobile'
        );
    
        foreach ($os_array as $regex => $value) { 
    
            if (preg_match($regex, $user_agent)) {
                $os_platform    =   $value;
            }
    
        }   
    
        return $os_platform;
    }

    /*
    * Browser
    *
    */
    function browser() {

        $user_agent = $_SERVER['HTTP_USER_AGENT'];
    
        $browser        = "Unknown Browser";
    
        $browser_array = array(
                                '/msie/i'      => 'Internet Explorer',
                                '/firefox/i'   => 'Firefox',
                                '/safari/i'    => 'Safari',
                                '/chrome/i'    => 'Chrome',
                                '/edge/i'      => 'Edge',
                                '/opera/i'     => 'Opera',
                                '/netscape/i'  => 'Netscape',
                                '/maxthon/i'   => 'Maxthon',
                                '/konqueror/i' => 'Konqueror',
                                '/mobile/i'    => 'Handheld Browser'
                         );
    
        foreach ($browser_array as $regex => $value)
            if (preg_match($regex, $user_agent))
                $browser = $value;
    
        return $browser;
    }

}