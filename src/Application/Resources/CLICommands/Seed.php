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
class Seed {
    
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
            'create : Create seeding file.',
            'run : Run seed file.',
            'all : Seed all.'
        ]);
    }
    
    /**
     * Create
     * 
    **/
    public function create($name)
    {
        $prefixNamespace = App::config()->db->seeds->namespace;
        $prefixNamespace = str_replace(['/', '//', '.'], '\\', $prefixNamespace);

        $dir = App::config()->db->seeds->path;
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
namespace '.$prefixNamespace.';

/*
|---------------------------------------------------
| Uses
|---------------------------------------------------
*/
use Kiaan\Database\Schema\Build\SeedBuild;
use Kiaan\DB;
use Kiaan\Auth;

/*
|---------------------------------------------------
| Seeds
|---------------------------------------------------
*/
class Users implements SeedBuild {

    /**
     * Handle
     * 
    **/
    public function handle()
    {
        DB::table("users")->insert([
            "name" => "Hassan",
            "email" => "kerdashhassan@gmail.com",
            "password" => Auth::hash("password")
        ]);
    }  

}';

        // Create file
        if ( !file_exists($dir) ) {mkdir($dir, 0744);}
        chmod($dir, 755);
        file_put_contents($dir."/$name.php", $content);

        // Success
        Cli::success(("Done, create '$name' seeding file."));
    }

    /**
     * Run
     * 
    **/
    public function run($class)
    {
        // Prefix
        $prefixNamespace = App::config()->db->seeds->namespace;
        $prefixNamespace = str_replace(['/', '//', '.'], '\\', $prefixNamespace);

        // Class
        $class = "\\$prefixNamespace\\$class";

        // Object
        $object = new $class;

        $method = App::config()->db->seeds->method;
        $func = call_user_func([$object, $method]);

        // Get short name
        $class = (new \ReflectionClass($class))->getShortName();

        // Success
        Cli::success(("Done, '$class' is seeded."));
    }

    /**
     * All
     * 
    **/
    public function all()
    {
        // Path
        $path = App::config()->db->seeds->path;
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
                shell_exec("php kiaan seed:run $class");

                // Success
                Cli::success(("Done, '$class' is seeded."));
            }
        }
    }

}