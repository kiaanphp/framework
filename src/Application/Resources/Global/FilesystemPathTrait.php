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
namespace Kiaan\Application\Resources\Global;

/*
|---------------------------------------------------
| Filesystem path trait
|---------------------------------------------------
*/
trait FilesystemPathTrait
{

    /**
     * Filesystem root
     *
    */
    protected $filesystemRoot = '';

    /**
     * Filesystem path
     *
    */
    protected $filesystemPath = '';

    /**
     * Filesystem root
     *
    */
    public function filesystemRoot(string $path=null) {
        if(is_null($path)){
            return $this->filesystemRoot;
        }

        return $this->filesystemRoot = $path;
    }

    /**
     * Filesystem path
     *
    */
    public function filesystemPath(string $path=null) {
        if(is_null($path)){
            return $this->filesystemPath;
        }

        return $this->filesystemPath = $path;
    }

    /**
     * Prepare filesystem root
     *
    */
    protected function filesystem_preoare($var) {
        // Filesystem path
        $filesystem = rtrim($var, DIRECTORY_SEPARATOR);
        $filesystem = empty($filesystem) ? $filesystem : $filesystem . DIRECTORY_SEPARATOR;

        return $filesystem;
    }

    /**
     * Prepare filesystem root
     *
    */
    protected function filesystem_root() {
        return $this->filesystem_preoare($this->filesystemRoot);
    }

    /**
     * Prepare filesystem path
     *
    */
    protected function filesystem_path() {
        return $this->filesystem_preoare($this->filesystemPath);
    }

    /**
     * Get path
     *
    */
    public function getPath() {
        return $this->filesystemPath;
    }

}