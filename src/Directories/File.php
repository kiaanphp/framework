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
| File
|---------------------------------------------------
*/
class File {
    
    /*
    * Global traits
    *
    */
    use \Kiaan\Application\Resources\Global\FilesystemPathTrait;
    use \Kiaan\Application\Resources\Global\ExtendingTrait;
     
    /**
     *  Traits
     * 
    */
    use File\Helpers;

    /**
    * Traits
    *
    */
    use File\Helpers;

    /**
     * Check that file exists
     *
     * @var string $path
     * @return bool
     */
    public function exist($path) {
        $path = $this->filesystemRoot() . $path;

        return file_exists($path);
    }

     /**
     * Delete file
     *
     * @var string $path
     * @return mixed
     */
    public function delete($path) {
        $path = $this->filesystemRoot() . $path;
        
        if (file_exists($path)) {
            return unlink($path);
        }
    }

    /**
     * Copy file
     *
     * @var string $path
     * @var string $second_path
     * @return mixed
     */
    public function copy($path, $second_path) {
        $path = $this->filesystemRoot() . $path;
        $second_path = $this->filesystemRoot() . $path;
        
        if (file_exists($path)) {
            return copy($path, $second_path);
        }
    }

    /**
     * Move file
     *
     * @var string $path
     * @var string $second_path
     * @return mixed
     */
    public function move($path, $second_path) {
        $path = $this->filesystemRoot() . $path;
        $second_path = $this->filesystemRoot() . $path;
        
        if (file_exists($path)) {
            $new_path = $second_path.'\\'.$this->fullName($path);

            //Move the file using PHP's rename function.
            return rename($path, $new_path);
        }
    }

     /**
     * Full name of file
     *
     * @var string $path
     * @return mixed
     */
    public function fullName($path) {
        $path = $this->filesystemRoot() . $path;

        if (file_exists($path)) {
            return pathinfo($path, PATHINFO_BASENAME);
        }
    }

     /**
     * Name of file
     *
     * @var string $path
     * @return mixed
     */
    public function name($path) {
        $path = $this->filesystemRoot() . $path;

        if (file_exists($path)) {
            $file = pathinfo($path, PATHINFO_FILENAME);
            return preg_replace('/\.[^.]+$/', '', $file);
        }
    }

    
    /**
     * Folder of file
     *
     * @var string $path
     * @return mixed
     */
    public function folder($path) {
        $path = $this->filesystemRoot() . $path;

        if (file_exists($path)) {
            return pathinfo($path, PATHINFO_DIRNAME);
        }
    }

    /**
     * Rename of file
     * @var string $path
     * @var string $name
     * 
     * @return mixed
     */
    public function rename($path, $name) {
        $path = $this->filesystemRoot() . $path;

        if (file_exists($path)) {
            $new_path = $this->folder($path).'\\'.$name.'.'.$this->extension($path);
            return rename($path , $new_path);
        }
    }

    /**
     * Extension of file
     *
     * @var string $path
     * @return mixed
     */
    public function extension($path) {
        $path = $this->filesystemRoot() . $path;

        if (file_exists($path)) {
            return pathinfo($path, PATHINFO_EXTENSION);
        }
    }

     /**
     * Get extension of file
     *
     * @var string $path
     * @var string $extension
     * @return mixed
     */
    public function getExtension($path, $extension) {
        $path = $this->filesystemRoot() . $path;

        if (file_exists($path)) {
            $file = pathinfo($path, PATHINFO_FILENAME);
            return preg_replace('/\.[^.]+$/', '.', $file) . '.' . $extension;
        }
    }

     /**
     * Set extension of file
     *
     * @var string $path
     * @var string $extension
     * @return mixed
     */
    public function setExtension($path, $extension) {
        $path = $this->filesystemRoot() . $path;

        if (file_exists($path)) {
            $new_path = $this->folder($path).'\\' . $this->getExtension($path, $extension);
            return rename($path , $new_path);
        }
    }

    /**
     * Is extension of file
     *
     * @var string $path
     * @var string $extension
     * @return mixed
     */
    public function isExtension($path, $extension) {
        $path = $this->filesystemRoot() . $path;

        if (file_exists($path)) {
            return (($this->extension($path, $extension)==$extension) ? true : false);
        }
    }

    /**
     * Is writable of file
     *
     * @var string $path
     * @return mixed
     */
    public function writable($path) {
        $path = $this->filesystemRoot() . $path;

        if (file_exists($path)) {
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
        $path = $this->filesystemRoot() . $path;

        if (file_exists($path)) {
            return is_readable($path);
        }
    }

    /**
     * Get contents of file
     *
     * @var string $path
     * @return mixed
     */
    public function get($path) {
        $path = $this->filesystemRoot() . $path;

        if (file_exists($path)) {
            return file_get_contents($path);
        }
    }

    /**
     * Set contents of file
     *
     * @var string $path
     * @var string $content
     * @return mixed
     */
    public function set($path, $content) {
        $path = $this->filesystemRoot() . $path;

        if (file_exists($path)) {
            return file_put_contents($path, $content);
        }
    }

    /**
     * Read from file
     *
     * @var string $path
     * @return mixed
     */
    public function read($path) {
        $path = $this->filesystemRoot() . $path;

        if (file_exists($path)) {
            return readfile($path);
        }
    }

    /**
     * Write to file
     *
     * @var string $path
     * @var string $content
     * @return mixed
     */
    public function write($path, $content) {
        $path = $this->filesystemRoot() . $path;

        if (file_exists($path)) {
            $file = fopen($path,"w");
            $file_content = fwrite($file, $content);
            fclose($file);
            return $file_content;
        }
    }

    /**
     * File create 
     *
     * @var string $path
     * @return mixed
     */
    public function create($path) {
        $path = $this->filesystemRoot() . $path;

        if (!(file_exists($path))) {
            $file = fopen($path,"w");
            fwrite($file,'');
            fclose($file);
            return true;
        }
        return false;
    }

    /**
     * File size
     *
     * @var string $path
     * @return mixed
     */
    public function size($path) {
        $path = $this->filesystemRoot() . $path;

        if (file_exists($path)) {
            return filesize($path);
        }
    }

    /**
     * Mime of file
     *
     * @var string $path
     * @return mixed
     */
    public function mime($path) {
        $path = $this->filesystemRoot() . $path;

        if (file_exists($path)) {
            return mime_content_type($path);
        }
    }

    /**
     *  Bloob
     *
     * @return string
     */
    public function blob($path) {
        $path = $this->filesystemRoot() . $path;

        if (file_exists($path)) {
        return file_get_contents(addslashes($path));
       }
    }

    /**
     * History of file 
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

        if (file_exists($path)) {
            if ($action=='update') { // update
                return filectime($path);
            }else{ // change
                return filemtime($path);
            }
        }
    }

    /**
     * Namespace extract form file
     * 
	*/
    public function namespace($file)
    {
        return $this->extract($file, 'namespace');
    }

    /**
     * Class name extract form file
     * 
	*/
    public function className($file)
    {
        return $this->extract($file, 'class');
    }

    /**
     * Class extract form file
     * 
	*/
    public function class($file)
    {
        return $this->namespace($file).'\\'.$this->className($file);
    }

}