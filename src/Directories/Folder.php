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
namespace Kiaan\Directories;

/*
|---------------------------------------------------
| Folder
|---------------------------------------------------
*/
class Folder {
    
    /*
    * Global traits
    *
    */
    use \Kiaan\Application\Resources\Global\FilesystemPathTrait;
    use \Kiaan\Application\Resources\Global\ExtendingTrait;
     
    /**
    * Traits
    *
    */
    use Folder\Helpers;

    /**
     * Construct
     * 
    */
    public function __construct(){}

    /**
     * Check that folder exists
     *
     * @var string $path
     * @return bool
     */
    public function exist($path) {
        $path = $this->filesystemRoot() . $path;

        return is_dir($path);
    }

     /**
     * Get all files and folders in folder
     *
     * @param string $path
     * @return mixed
     */
    public function get($path) {
        $path = $this->filesystemRoot() . $path;

        $ListFiles = array();
        $Files = array();

        $scannedFiles  = array_diff(scandir($path), ['.', '..']);
        $Files = array_merge($scannedFiles, $ListFiles);

        return $Files;
    }

    /**
     * Get all files in folder
     *
     * @param string $path
     * @return mixed
     */
    public function files($path) {
        $path = $this->filesystemRoot() . $path;

        return $this->list_directory($path, 'files');
    }

    /**
     * Get all files in dirs
     *
     * @param string $path
     * @return mixed
     */
    public function filesDirs($path) {
        $path = $this->filesystemRoot() . $path;

        return $this->listdirsfiles($path);
    }

    /**
     * get all folders in folder
     *
     * @param string $path
     * @return mixed
     */
    public function folders($path) {
        $path = $this->filesystemRoot() . $path;

        return $this->list_directory($path, 'folders');
    }
    
    /**
     * get all dirs in path
     *
     * @param string $path
     * @return mixed
     */
    public function dirs($path) {
        $path = $this->filesystemRoot() . $path;

        return $this->listdirs($path);
    }

    /**
     * Cleanup folder
     *
     * @param string $path
     * @return mixed
     */
    public function clean($path) {
        $path = $this->filesystemRoot() . $path;

        // destroy
        if (is_dir($path)){
            $this->unlinkr($path);
            return true;
        }else{
            return false;
        }

    }

    /**
     * Delete folder
     *
     * @param string $path
     * @return mixed
     */
    public function delete($path) {
        $path = $this->filesystemRoot() . $path;

        if (is_dir($path)){
            $this->unlinkr($path);
            if(rmdir($path)) {
                return true;
              }else{
                return false;
              }
        }else{
            return false;
        }
    }

    /**
     * Create folder
     *
     * @param string $path
     * @return mixed
     */
    public function create($path, $permissions="0777") {
        $path = $this->filesystemRoot() . $path;

        if(!is_dir($path)){
            mkdir($path, $permissions, true);
            return true;
        }else{
            return false;
        }
    }

     /**
     * Copy folder
     *
     * @var string $path
     * @var string $second_path
     * @return mixed
     */
    public function copy($path, $second_path) {
        $path = $this->filesystemRoot() . $path;
        $second_path = $this->preparePathFileSystem($second_path);

        if(is_dir($path)){
            return $this->copy_directory($path, $second_path);
        }
    }

    /**
     * Name of folder
     *
     * @var string $path
     * @return mixed
     */
    public function name($path) {
        $path = $this->filesystemRoot() . $path;

        if (is_dir($path)) {
            $file = pathinfo($path, PATHINFO_FILENAME);
            return preg_replace('/\.[^.]+$/', '', $file);
        }
    }
    
    /**
     * Move folder
     *
     * @var string $path
     * @var string $second_path
     * @return mixed
     */
    public function move($path, $second_path) {
        $path = $this->filesystemRoot() . $path;
        $second_path = $this->preparePathFileSystem($second_path);

        if (is_dir($path)) {
        $new_path = $second_path.'\\'.$this->name($path);
        //Move the file using PHP's rename function.
        return $folderMoved = rename($path, $new_path);
        }
    }

    /**
     * Rename of folder
     * @var string $path
     * @var string $name
     * 
     * @return mixed
     */
    public function rename($path, $name) {
        $path = $this->filesystemRoot() . $path;

        if (is_dir($path)) {
            $new_path = pathinfo($path, PATHINFO_DIRNAME).'\\'.$name;
            return rename($path , $new_path);
        }
    }

   /**
     * Is writable of folder
     *
     * @var string $path
     * @return mixed
     */
    public function writable($path) {
        $path = $this->filesystemRoot() . $path;

        if (is_dir($path)) {
            return is_writable($path);
        }
    }

    /**
     * Is readable of folder
     *
     * @var string $path
     * @return mixed
     */
    public function readable($path) {
        $path = $this->filesystemRoot() . $path;

        if (is_dir($path)) {
            return is_readable($path);
        }
    }

    /**
     * Folder size
     *
     * @var string $path
     * @return mixed
     */
    public function size($path) {
        $path = $this->filesystemRoot() . $path;

        if (is_dir($path)) {
            return $this->dirSize($path);
        }
    }

    /**
     * History of folder 
     *
     * @var string $path
     * @var string $action ['update', 'change']
     * @return mixed
     */
    public function history($path, $action='update') {
        $path = $this->filesystemRoot() . $path;

        // Action
        $action_list = array('update', 'change');
        if (!in_array($action, $action_list)){$action="update";}

        if (is_dir($path)) {
            if ($action=='update') { // update
                return filectime($path);
            }else{ // change
                return filemtime($path);
            }
        }
    }

}