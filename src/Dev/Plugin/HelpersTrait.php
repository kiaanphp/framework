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
namespace Kiaan\Dev\Plugin;

/*
|---------------------------------------------------
| Helpers trait
|---------------------------------------------------
*/
trait HelpersTrait {

    /**
     * Get root of app
     *
     * @param string $path
     * @return string $path
     */
    protected function root($path='') {
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
     * Discovery services
     * 
    **/
    protected function discoveryServices()
    {
        $content = "<?php \n \n";

        if(is_array($this->discoveryList()) && count($this->discoveryList())!=0){
            foreach($this->discoveryList() as $plugin){
                $plugin_class = str_replace(['/', '.'], "\\", $plugin['class']);
                $plugin_function = $plugin['function'];

                $content .= "(new \\$plugin_class)->{$plugin_function}(); \n";
            }
        }

        file_put_contents($this->tempDiscoveryServices, $content);
    }
    
   /**
     * List
    */
    protected function list(){
        $autoload_psr4 = $this->root("vendor".DIRECTORY_SEPARATOR."composer".DIRECTORY_SEPARATOR."autoload_psr4.php");
        return (include $autoload_psr4);
    }

}