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
class Migrate {
    
    /**
     * Handle
     * 
    **/
    public function handle()
    {
        return [
            "menu" => "menu_handle",
            "create" => "create",
            "run" => "run",
            "all" => "all"
        ];
    }

    /**
     * Menu handle
     * 
    **/
    public function menu_handle()
    {
        Cli::menu([
            'create : Create migrate file.',
            'run : Run migrate file.',
            'all : Migrate all.'
        ]);
    }
    
    /**
     * Create
     * 
    **/
    public function create($name)
    {
        $namespace = App::config()->db->migrations->namespace;
        $namespace = str_replace(['/', '//', '.'], '\\', $namespace);

        $dir = App::config()->db->migrations->path;
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
use Kiaan\Database\Schema\Build\MigrateBuild;
use Kiaan\Schema;

/*
|---------------------------------------------------
| Migrations
|---------------------------------------------------
*/
class '.$name.' implements MigrateBuild {

    /**
     * Handle
     * 
    **/
    public function handle()
    {
        Schema::createTable("'.$name.'")
        ->key("id")->notNull()->primary()->auto()
        ->submit();
    } 

}';

        // Create file
        if ( !file_exists($dir) ) {mkdir($dir, 0744);}
        chmod($dir, 755);
        file_put_contents($dir."/$name.php", $content);

        // Success
        Cli::success(("Done, create '$name' migration file."));
    }

    /**
     * Run
     * 
    **/
    public function run($class)
    {
        // Prefix
        $prefixNamespace = App::config()->db->migrations->namespace;
        $prefixNamespace = str_replace(['/', '//', '.'], '\\', $prefixNamespace);

        // Class
        $class = "\\$prefixNamespace\\$class";

        // Object
        $object = new $class;
        
        // Function
        $method = App::config()->db->migrations->method;
        $func = call_user_func([$object, $method]);

        // Get short name
        $class = (new \ReflectionClass($class))->getShortName();

        // Success
        Cli::success(("Done, '$class' is migrated."));
    }

    /**
     * All
     * 
    **/
    public function all()
    {
        // Path
        $path = App::config()->db->migrations->path;
        $path = str_replace(['/', '//', '.'], '\\', $path);
        
        // Get Files
        $ListFiles = array();
        $scannedFiles  = array_diff(scandir($path), ['.', '..']);
        $ListFiles = array_merge($scannedFiles, $ListFiles);

        // Check extension of file
        foreach($scannedFiles as $file)
        {
            if(pathinfo($file, PATHINFO_EXTENSION) == 'php') {
                // Class
                $class = pathinfo($file)['filename'];

                // Shell
                shell_exec("php kiaan migrate:run $class");

                // Success
                Cli::success(("Done, '$class' is migrated."));
            }
        }
    }

}