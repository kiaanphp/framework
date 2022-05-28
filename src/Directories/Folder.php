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

    /**
    * Traits
    *
    */
    use Folder\FileSystemTrait;
    use Folder\Helpers;

    /**
     * Construct
     * 
    */
    public function __construct(){}

    /**
     * Root enable
     * 
    */
    public function rootEnable($value=true) {
        return $this->root_enable = $value;
    }

    /**
     * Check that folder exists
     *
     * @var string $path
     * @return bool
     */
    public function exist($path) {
        $path = $this->preparePathFileSystem($path);

        return is_dir($path);
    }

     /**
     * get all files and folders in folder
     *
     * @param string $path
     * @return mixed
     */
    public function get($path) {
        $path = $this->preparePathFileSystem($path);

        $ListFiles = array();
        $Files = array();

        $scannedFiles  = array_diff(scandir($path), ['.', '..']);
        $Files = array_merge($scannedFiles, $ListFiles);

        return $Files;
    }

    /**
     * get all files in folder
     *
     * @param string $path
     * @return mixed
     */
    public function files($path) {
        $path = $this->preparePathFileSystem($path);

        return $this->list_directory($path, 'files');
    }

    /**
     * get all files in dirs
     *
     * @param string $path
     * @return mixed
     */
    public function filesDirs($path) {
        $path = $this->preparePathFileSystem($path);

        return $this->listdirsfiles($path);
    }

    /**
     * get all folders in folder
     *
     * @param string $path
     * @return mixed
     */
    public function folders($path) {
        $path = $this->preparePathFileSystem($path);

        return $this->list_directory($path, 'folders');
    }
    
    /**
     * get all dirs in path
     *
     * @param string $path
     * @return mixed
     */
    public function dirs($path) {
        $path = $this->preparePathFileSystem($path);

        return $this->listdirs($path);
    }

    /**
     * Cleanup folder
     *
     * @param string $path
     * @return mixed
     */
    public function clean($path) {
        $path = $this->preparePathFileSystem($path);

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
        $path = $this->preparePathFileSystem($path);

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
        $path = $this->preparePathFileSystem($path);

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
        $path = $this->preparePathFileSystem($path);
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
        $path = $this->preparePathFileSystem($path);

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
        $path = $this->preparePathFileSystem($path);
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
        $path = $this->preparePathFileSystem($path);

        if (is_dir($path)) {
            $new_path = pathinfo($path, PATHINFO_DIRNAME).'\\'.$name;
            return rename($path , $new_path);
        }
    }

   /**
     * Is writable of file
     *
     * @var string $path
     * @return mixed
     */
    public function writable($path) {
        $path = $this->preparePathFileSystem($path);

        if (is_dir($path)) {
            return is_writable($path);
        }
    }

    /**
     * Is readable of file
     *
     * @var string $path
     * @return mixed
     */
    public function readable($path) {
        $path = $this->preparePathFileSystem($path);

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
        $path = $this->preparePathFileSystem($path);

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
        $path = $this->preparePathFileSystem($path);

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