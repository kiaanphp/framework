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
namespace Kiaan\Http\Input;

/*
|---------------------------------------------------
| File
|---------------------------------------------------
*/
class File {

    /**
     * Class
     * 
    */
    protected $class;

    /**
     * Files
     * 
    */
    protected $selectedFile;

    /**
     * Files
     * 
    */
    protected $files;

    /**
     * Request constructor and magic methods
     *
     * @return void
     */
    public function __construct($class, $files, $selectedFile){
        $this->files = $files;
        $this->files = $files;
        $this->selectedFile = $selectedFile;
    }

    /**
     * Get file info
     *
    */
    public function info() {
        $key = $this->selectedFile;
        $this->selectedFile = '';

       return $this->files[$key];
    }

    /**
     * Save file
     *
     * @param string $key
     * @return string $value
    */
    public function save($path) {
        $path = $this->class->filesystemRoot() . $path;
        
        // tmp path
        $tmp = $this->files[$this->selectedFile]["tmp_name"];
        $this->selectedFile = '';

        // upload
        if(move_uploaded_file($tmp, $path))
        {
            // true
            return true;
        }
        else
        {
            if(file_exists($tmp)){
                // upload
                rename($tmp, $path);

                // true
                return true;
            }else{
                // Throwable
                return $this->files[$this->selectedFile]["error"];
            }
        }
    }

    /**
     *  Bloob
     *
     * @return string
     */
    public function blob($file) {
        return file_get_contents(addslashes($this->files[$file]['tmp_name']));
    }

}