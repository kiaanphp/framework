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
| Plugin
|---------------------------------------------------
*/
class Plugin {
    
    /*
    * Global traits
    *
    */
    use \Kiaan\Application\Resources\Global\ExtendingTrait;
     
    /**
    * Traits
    *
    */
    use Plugin\HelpersTrait;

    /**
    * Temp discovery file
    *
    */
    protected $tempDiscoveryFile;

    /**
    * Temp discovery services file
    *
    */
    protected $tempDiscoveryServices;

    /**
    * Handle 
    *
    */
    protected $handle = "handle";

    /**
    * Extra 
    *
    */
    protected $extra = [
        "services" => "framework-services-class",
        "servicesEnable" => "framework-services-enable",
        "servicesMethod" => "framework-services-method",
    ];

    /**
     * Construct
     * 
    */
    public function __construct(){
        // Temp discovery file
        $this->tempDiscoveryFile = __DIR__.DIRECTORY_SEPARATOR.'Plugin'.DIRECTORY_SEPARATOR.'Resources'.DIRECTORY_SEPARATOR.'TempDiscovery.php';

        // Temp discovery file
        $this->tempDiscoveryServices = __DIR__.DIRECTORY_SEPARATOR.'Plugin'.DIRECTORY_SEPARATOR.'Resources'.DIRECTORY_SEPARATOR.'TempDiscoveryServices.php';
    }

    /**
     * Discovery
     * 
    **/
    public function discovery()
    {
        $plugins = array();

        foreach($this->list() as $plugin){
            $plugin = $plugin[0];

            $composer = dirname($plugin).DIRECTORY_SEPARATOR.'composer.json';
            
            if(file_exists($composer)){
                $data = json_decode(file_get_contents($composer));
                $check = $data->extra->{$this->extra['servicesEnable']} ?? false;

                if($check){
                    $class_check = ($data->extra->{$this->extra['services']}) ?? false;
                    $function_check = ($data->extra->{$this->extra['servicesMethod']}) ?? false;
                    
                    $plugins[$data->name] = [
                        "path" => $plugin,
                        "class" => ($class_check) ? $data->extra->{$this->extra['services']} : false,
                        "function" => ($function_check) ? $data->extra->{$this->extra['servicesMethod']} : false
                    ];
                }
            }
        }

        // Save to temp discovery file
        file_put_contents($this->tempDiscoveryFile, "<?php \n \n return ".var_export($plugins, true).";");

        // Save to temp discovery services file
        $this->discoveryServices();

        return $plugins;
    }

    /**
     * Services run
     * 
    **/
    public function servicesRun()
    {
       return include $this->tempDiscoveryServices;
    }

    /**
     * Discovery clean
     * 
    **/
    public function discoveryClean()
    {
        file_put_contents($this->tempDiscoveryFile, "");
        file_put_contents($this->tempDiscoveryServices, "");

        return true;
    }

    /**
     * Discovery list
     * 
    **/
    public function DiscoveryList()
    {       
        return include($this->tempDiscoveryFile);
    }

   /**
     * info
    */
    public function info($key){
        // Key
        $key = trim($key, "\\");
        $key = str_replace([".", "/"], "\\", $key);
        $key .= "\\";

        // Info
        $list = $this->list();
        $plugin = $list[$key][0];
        $composer = dirname($plugin).DIRECTORY_SEPARATOR.'composer.json';
        if(file_exists($composer)){
            $data = json_decode(file_get_contents($composer));
            return $data;
        }
        
        return false;
    }

    /**
     * Set handle
    */
    public function setHandle($value){
        $this->handle = $value;

        return clone($this);
    }

    /**
     * Get handle
    */
    public function getHandle(){
        return $this->handle;
    }
    
    /**
     * Set extra
    */
    public function setExtra($value){
        $this->extra = $value;

        return clone($this);
    }

    /**
     * Get extra
    */
    public function getExtra(){
        return $this->extra;
    }

    /**
     * Command
    */
    public function command($class, $func=false){
        // Class
        $class = str_replace([".", "/"], "\\", $this->info($class)->extra->{$this->extra['services']});
        $class = new $class;
        
        if(!$func){
            $handle = $this->getHandle();
        }else{
            $handle = $this->getHandle();
            $handle = $class->$handle()[$func];
        }

        // Run submit
        return $class->$handle();
    }

}