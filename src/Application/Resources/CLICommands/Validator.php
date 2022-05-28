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
namespace Kiaan\Application\Resources\CLICommands;

/*
|---------------------------------------------------
| Uses
|---------------------------------------------------
*/
use Kiaan\Cli;
use Kiaan\App;
use Kiaan\Url;

/*
|---------------------------------------------------
| Class
|---------------------------------------------------
*/
class Validator {

    /**
     * Handle
     * 
    **/
    public function handle()
    {
        return [
            "menu" => "menu_handle",
            "create" => "create"
        ];
    }

    /**
     * Menu handle
     * 
    **/
    public function menu_handle()
    {
        Cli::menu([
            'create : Create validator file.'
        ]);
    }

    /**
     * Create
     * 
    **/
    public function create($name)
    {

        $namespace = App::config()->validator->namespace;
        $namespace = str_replace(['/', '//', '.'], '\\', $namespace);

        $dir = App::config()->validator->path;
        $dir = str_replace(['/', '//', '.'], '\\', $dir);
        $dir = Url::root($dir);
        $content = '<?php

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
namespace '.$namespace.';

/*
|---------------------------------------------------
| Uses
|---------------------------------------------------
*/
use Kiaan\Security\Validator\Build\ValidatorBuild;

/*
|---------------------------------------------------
| Class
|---------------------------------------------------
*/
class '.$name.' implements ValidatorBuild {

    /**
     * handle
     * 
    **/
    public function handle()
    {
        return [
            "rule" => "rule",
            "message" => "message",
            "text" => "text"
        ];
    }    

    /**
     * Rule
     * 
     * $field string
     * $parms array
     * @return bool
     * 
    **/
    public function rule($name, $value, $parms)
    {
        // Check
        if($field=="Hello_World"){
            // True
            return true;
        }else{
            // False
            return false;
        }
    }    

    /**
     * Message
     * 
     * $name string
     * @return string
     * 
    **/
    public function message($name, $value, $parms){
        return "Hello \'$name\'.";
    }

    /**
     * Text 
     * 
     * $name string
     * $message string
     * @return string
     * 
    **/
    public function text($name, $value, $parms, $message){
        return $message;
    }

}';

        // Create file
        if ( !file_exists($dir) ) {mkdir($dir, 0744);}
        chmod($dir, 755);
        file_put_contents($dir."/$name.php", $content);

        // Success
        Cli::success(("Done, create '$name' validator file."));
    }

}