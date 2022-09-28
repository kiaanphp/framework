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
namespace Kiaan\Dev\Cli;

/*
|---------------------------------------------------
| Helpers trait
|---------------------------------------------------
*/
trait HelpersTrait {

    /**
     * init Colored String
     *
     * @param string      $string black|dark_gray|blue|light_blue|green|light_green|cyan|light_cyan|red|light_red|purple|brown|yellow|light_gray|white
     * @param string|null $foregroundColor black|red|green|yellow|blue|magenta|cyan|light_gray
     * @param string|null $backgroundColor $foregroundColor
     *
     * @return string
     */
    protected function initColoredString(
        $string,
        $foregroundColor = null,
        $backgroundColor = null
    ) {
        $coloredString = '';

        if (isset($this->foregroundColors[$foregroundColor])) {
            $coloredString .= "\033[".$this->foregroundColors[$foregroundColor].'m';
        }
        if (isset($this->backgroundColors[$backgroundColor])) {
            $coloredString .= "\033[".$this->backgroundColors[$backgroundColor].'m';
        }

        $coloredString .= $string."\033[0m";

        return $coloredString;
    }

    /**
     * function.
     *
    */
    protected function function()
    {
        // Args
        $argv = $_SERVER['argv'][0] ?? null;
        $argv = explode(':',$argv);

        // Function
        $function = $argv[1] ?? $this->defaultMethod();
        
        return $function;
    }

    /**
     * Parameters.
     *
    */
    protected function parms()
    {
        unset($_SERVER['argv'][0]);
        $parms =  $_SERVER['argv'];

        return $parms;
    }

    /**
     * Default method
     *
    */
    public function defaultMethod()
    {
        // Args
        $argv = $_SERVER['argv'][0] ?? null;
        $argv = explode(':',$argv);

        // Command
        $command = $argv[0] ?? null;
        $command = ($this->commands)[$command] ?? null;
        $command = explode(',', $command)[1] ?? $this->getDefaultMethod();
        $command = trim($command);
        
        return $command;
    }
    
    /**
     * command.
     *
    */
    public function command()
    {
        // Args
        $argv = $_SERVER['argv'][0] ?? null;
        $argv = explode(':',$argv);

        // Command
        $command = $argv[0] ?? null;
        $command = ($this->commands)[$command] ?? null;
        
        if(!is_null($command)){
            $command = trim(explode(',', $command)[0]);
            $command = $command ?? null;
        }else{
            $this->error(("   Command not found.   "), 'light_gray');

            // Exit
            exit(); 
        }

        if (!class_exists($command)) { 
            $command = $this->getNamespace() . '\\' . $command;
            $command = $command;
        }

        return $command;
    }

}