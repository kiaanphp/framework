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
namespace Kiaan\Dev;

/*
|---------------------------------------------------
| Cli
|---------------------------------------------------
*/
class Cli {

    /**
    * Traits
    *
    */
    use Cli\HelpersTrait;
    
    /*
    * Global traits
    *
    */
    use \Kiaan\Application\Resources\Global\ExtendingTrait;
     
    /**
    * Namespace
    *
    */
    protected $namespace;

    /**
    * Path
    *
    */
    protected $path;

    /**
    * Method
    *
    */
    protected $method = 'handle';
    
    /**
     * Commands
     * 
    */
    private $commands = array(); 

    /**
     * foreground colors
     * 
    */
    private $foregroundColors = [
        'black'        => '0;30',
        'dark_gray'    => '1;30',
        'blue'         => '0;34',
        'light_blue'   => '1;34',
        'green'        => '0;32',
        'light_green'  => '1;32',
        'cyan'         => '0;36',
        'light_cyan'   => '1;36',
        'red'          => '0;31',
        'light_red'    => '1;31',
        'purple'       => '0;35',
        'light_purple' => '1;35',
        'brown'        => '0;33',
        'yellow'       => '1;33',
        'light_gray'   => '0;37',
        'white'        => '1;37',
    ];
    
    /**
     * background colors
     * 
    */
    private $backgroundColors = [
        'black'      => '40',
        'red'        => '41',
        'green'      => '42',
        'yellow'     => '43',
        'blue'       => '44',
        'magenta'    => '45',
        'cyan'       => '46',
        'light_gray' => '47',
    ];

    /**
     * Construct
     * 
    */
    public function __construct(){}

    /**
     * Get namespace
     * 
    */
    public function getNamespace() {
        return $this->namespace;
    }

    /**
     * Set namespace
     * 
    */
    public function setNamespace($value) {
        $this->namespace = $value;

        return clone($this);
    }

    /**
     * Get path
     * 
    */
    public function getPath() {
        return $this->path;
    }

    /**
     * Get path
     * 
    */
    public function setPath($path) {
        return $this->path = $path;
    }

    /**
     * Get method
     * 
    */
    public function getMethod() {
        return $this->method;
    }

    /**
     * Get commands
     * 
    **/
    public function getCommands(){
        return $this->commands;
    }

    /**
     * Set commands
     * 
    **/
    public function setCommands(array $list){
        $list = array_map('trim', $list);
        return $this->commands = $list;
        
    }

    /**
     * Commands
     * 
    **/
    public function commands(array $list){
        $list = array_map('trim', $list);
        return $this->commands = array_merge($this->commands, $list);
    }

    /**
     * output.
     *
     * @param $msg
     */
    public function output($msg){
        fwrite(STDOUT, $this->initColoredString($msg, null,'red').PHP_EOL);
    }

    /**
     * notice.
     *
     * @param $msg
     */
    public function notice($msg, $bg='')
    {
        fwrite(STDOUT, $this->initColoredString($msg, 'light_gray', $bg).PHP_EOL);
    }

    /**
     * error.
     *
     * @param $msg
     */
    public function error($msg, $bg='')
    {
        fwrite(STDERR, $this->initColoredString($msg, 'red', $bg).PHP_EOL);
    }

    /**
     * warn.
     *
     * @param $msg
     */
    public function warn($msg, $bg='')
    {
        fwrite(STDOUT, $this->initColoredString($msg, 'yellow', $bg).PHP_EOL);
    }

    /**
     * success.
     *
     * @param $msg
     */
    public function success($msg, $bg='')
    {
        fwrite(STDOUT, $this->initColoredString($msg, 'green', $bg).PHP_EOL);
    }

    /**
     * Menu.
     *
    */
    public function menu($array)
    {
        // Index
        $index = 1;

        // Colors
        $colors = [
            "red",
            "yellow",
            "green",
            "light_gray",
            "blue",
            "light_purple",
            "white",
            "dark_gray",
            "bink",
            "gray",
            "light_blue",
            "light_green",
            "purple",
            "cyan",
            "brown",
            "light_red",
            "light_cyan",
        ];

        // Menu
        foreach($array as $key => $item) {
            if(is_numeric($key)){$key=$item;}
            $str = $index . '. ' . $key;

            //$this->warn($str);
            fwrite(STDOUT, $this->initColoredString($str, "black",$colors[($index - 1)]).PHP_EOL);

            $index++;
        }

        // Exit
        exit();
    }

    /**
     * Handle.
     *
    */
    public function handle()
    {
        // CLI mode
        if(PHP_SAPI == 'cli'){
            // Args
            array_shift($_SERVER['argv']);
            
            // Command
            $command = $this->command();

            if(empty($command)){
                $this->menu($this->commands);
                exit();
            }
            
            // Function
            $function = $this->function();

            // Excute
            $object = new $command;

            try {
                // Access to function
                $handle_func = $object->{$this->method}();

                if(array_key_exists($function, $handle_func)){
                    // Acess to target function
                    $result = call_user_func_array([$object, $handle_func[$function]], $this->parms());
                    if(is_string($result)){ echo $result; }
                }else{
                    $this->error(("   Oops, command not found.   "), 'light_gray');
                }

            } catch (\Throwable $th) {
                $this->error(("   Oops, something went wrong.   "), 'light_gray');
                $this->error(($th));
            }

        }
    }

    /**
     * Call.
     *
    */
    public function call()
    {
        // Args
        $args = func_get_args();

        // Command
        $command = $args[0] ?? null;
        $command = ($this->commands)[$command] ?? null;

        if(!is_null($command)){
            if (!class_exists($command)) { 
                $command = $this->getNamespace() . '/' . $command;
                $command = $command;
            }
        }else{
            throw new \Exception("Oops, command not found.");
        }
        
        $object = new $command;

        // Function
        $function = $args[1] ?? null;

        if(is_null($function)){
            throw new \Exception("Oops, something went wrong.");
        }

        // Parms
        array_shift($args);
        array_shift($args);

        // Excute
        try {
            // Access to function
            $handle_func = call_user_func_array([$object, $this->method], []);
            if(array_key_exists($function, $handle_func)){
                // Acess to target function
                call_user_func_array([$object, $handle_func[$function]], $args);
            }else{
                throw new \Exception("Oops, command not found.");
            }

        } catch (\Throwable $th) {
            throw new \Exception("Oops, something went wrong.");
        }
    }

    /**
     * Exec.
     *
    */
    public function exec($command)
    {
        return shell_exec($command);
    }

}