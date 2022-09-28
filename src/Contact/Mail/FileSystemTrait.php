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
namespace Kiaan\Contact\Mail;

/*
|---------------------------------------------------
| FileSystem Trait
|---------------------------------------------------
*/
trait FileSystemTrait
{

    /**
     * FileSystem root path
     * 
    */
    protected $fileSystemRootPath;

    /**
     * FileSystem folder path
     * 
    */
    protected $fileSystemFolderPath;

    /**
     * FileSystem mode
     * 
    */
    protected $fileSystemMode = 'path';

    /**
     * FileSystem mode
     * 
    */
    protected $tempfileSystemValueMode;

    /**
     * Temp fileSystem mode
     * 
    */
    protected $tempFileSystemMode = false;

    /**
     * Set root path
     *
    */
    public function setRootPath($path) {
        return $this->fileSystemRootPath = $path;
    }

    /**
     * Get root path
     *
    */
    public function getRootPath() {
        return $this->fileSystemRootPath;
    }

    /**
     * Set folder path
     * 
    */
    public function setFolderPath($path) {
        return $this->fileSystemFolderPath = $path;
    }

    /**
     * Get folder path
     * 
    */
    public function getFolderPath() {
        return $this->fileSystemFolderPath;
    }

    /**
     * FileSystem
     *
    */
    public function fileSystem($mode='path', $notTemp=false) {
        // Temp
        if($notTemp){
            $this->tempFileSystemMode = false;
        }else{
            $this->tempfileSystemValueMode = $this->fileSystemMode;
            $this->tempFileSystemMode = true;
        }
        
        // Mode
        $mode = trim($mode);

        switch ($mode) {
            case 'path':
                $this->fileSystemMode = 'path';
                return clone($this);
                break;
            case 'full':
                $this->fileSystemMode = 'full';
                return clone($this);
                break;
            case 'folder':
                $this->fileSystemMode = 'folder';
                return clone($this);
                break;
            default:
                $this->fileSystemMode = 'path';
                return clone($this);
                break;
        }
    }

    /**
     * Get filesystem
     *
    */
    public function getFileSystem() {
        return $this->fileSystemMode;
    }

    /**
     * Prepare path filesystem
     *
    */
    protected function preparePathFileSystem($path) {

        // Prepare path
        switch ($this->fileSystemMode) {
            case 'path':
                $preparePath = $this->getRootPath() . '/' . $path;
                break;
            case 'full':
                $preparePath = $path;
                break;
            case 'folder':
                $preparePath = $this->getFolderPath() . '/' . $path;
                break;
            default:
                $preparePath = $this->getRootPath() . '/' . $path;
                break;
        }

        // Temp
        if($this->tempFileSystemMode){
            $this->fileSystemMode = $this->tempfileSystemValueMode;
            $this->tempfileSystemValueMode = null;
            $this->tempFileSystemMode = false;
        }

        return $preparePath;
    }

}

