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
| Url
|---------------------------------------------------
*/
class Url {
    
    /*
    * Global traits
    *
    */
    use \Kiaan\Application\Resources\Global\ExtendingTrait;
         
    /**
     * Public folder path
     * 
    */
    protected $public_path;

    /**
     * Get public folder path
     * 
    */
    public function getPublicPath() {
        return $this->public_path;
    }
  
    /**
    * Set public folder path
    * 
    */
      public function setPublicPath($path) {
          return $this->public_path = $path;
    }

    /**
     * Get root of server
     *
     * @param string $path
     */
    public function serverRoot($path='') {
        return $_SERVER['DOCUMENT_ROOT'].'/'.$path;
    }

    /**
     * Get root
     *
     * @param string $path
     * @return string $path
     */
    public function root($path='') {
        if (PHP_SAPI != 'cli'){
            $script_filename = str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME']);
            $script_filename = explode("/", $script_filename);
            $script_filename = $script_filename[count($script_filename)-1];

            $script_folder = trim(preg_replace('/' . $script_filename . '/', '/', $_SERVER['SCRIPT_FILENAME'], 1), '/');

            $path = $script_folder . DIRECTORY_SEPARATOR . trim($path, '/');
            $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
            return $path;
        }else{
            $path = getcwd() . DIRECTORY_SEPARATOR . trim($path, '/');
            $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
            return $path;
        }
    }

    /**
     * Get root public
     *
     * @param string $path
     */
    public function publicRoot($path='') {
        $public_path = $this->getPublicPath();

        return $this->root($public_path.'/'.$path);
    }

    /**
     * Get url of server
     *
     * @param string $path
     * @return string $path
     */
    public function serverLink($path='') {
        $protocol = ($_SERVER['REQUEST_SCHEME'] ?? 'http') . '://';
        $host = $_SERVER['HTTP_HOST'] ?? null;

        $url = trim($protocol . $host . '/' . $path, '/');

        return $url;
    }

    /**
     * Get url
     *
     * @param string $path
     * @return string $path
     */
    public function link($path='') {
        $protocol = ($_SERVER['REQUEST_SCHEME'] ?? 'http') . '://';
        $host = $_SERVER['HTTP_HOST'] ?? null;
        
        $script_name = str_replace('\\', '', dirname($_SERVER['SCRIPT_NAME']));
        $script_name = $script_name . '/' . trim($path, '/');
        
        return trim($protocol . $host . $script_name, '/');
    }

    /**
     * Get public
     *
     * @param string $path
     * @return string $path
     */
    public function public($path='') {
        $public_path = $this->getPublicPath();
        
        return $this->link($public_path.'/'.$path);
    }

    /**
     * redirect to url
     *
     * @return bool
     */
    public function go($url) {
        header('location: ' . $url);
        exit();
    }

    /**
     * URL back
     *
     * @return bool
     */
    public function back() {
        return $_SERVER['HTTP_REFERER'] ?? null;
    }

    /**
     * Redirect to back
     *
     */
    public function goBack() {
        $url = $_SERVER['HTTP_REFERER'] ?? null;
        header('location: ' . $url);
        exit();
    }

    /**
     * URL encode
     *
     */
    public function encode($path) {
        return urlencode($path);
    }

    /**
     * URL decode
     *
     */
    public function decode($path) {
        return urldecode($path);
    }
    
}