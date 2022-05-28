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
class Model {
    
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
        Cli::menu(['create']);
    }
    
    /**
     * Create
     * 
    **/
    public function create($name, $table='')
    {
        $prefixNamespace = App::config()->db->models->namespace;
        $prefixNamespace = str_replace(['/', '//', '.'], '\\', $prefixNamespace);

        $dir = App::config()->db->models->path;
        $dir = str_replace(['/', '//', '.'], '\\', $dir);
        $dir = Url::root($dir);

        // Args
        if(empty($table)){
            $table = strtolower($name);
        }

        // Content
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
namespace '.$prefixNamespace.';

/*
|---------------------------------------------------
| Uses
|---------------------------------------------------
*/
use Kiaan\Database\DB\Build\ModelBuild;
use Kiaan\Database\DB\Build\Model;

/*
|---------------------------------------------------
| Model
|---------------------------------------------------
*/
class '.$name.' extends Model implements ModelBuild {

    /*
    * Class
    *
    */
    protected static $__CLASS__ = __CLASS__; 

    /*
    * Table
    *
    */
    protected $table = "'.$table.'"; 

    /*
    * Primary key
    *
    */
    protected $primary_key = "id";

    /* 
    * Foreign keys
    *
    */
    public $foreign_keys = [
    
    ];

    /*
    * Functions
    *
    */
    public function _function(){

    }

}';

        // Create file
        if ( !file_exists($dir) ) {mkdir($dir, 0744);}
        chmod($dir, 755);
        file_put_contents($dir."/$name.php", $content);

        // Success
        Cli::success(("Done, create '$name' model file."));
    }

}