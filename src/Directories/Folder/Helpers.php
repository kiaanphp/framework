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
namespace Kiaan\Directories\Folder;

/*
|---------------------------------------------------
| Helpers
|---------------------------------------------------
*/
trait Helpers {

    /**
     * list directory
     *
     * @param string $path
     * @return mixed
     */
    protected function listdirsfiles($path) {
        $files_list = array();
        $files_main_dir = $this->list_directory($path, 'files');
       
        // files main dir
        foreach ($files_main_dir as $file_main_dir) {
        array_push($files_list, $path.DIRECTORY_SEPARATOR.$file_main_dir);
        }
        
        // files sub dirs
        $folders = $this->dirs($path);
       
        foreach ($folders as $keyFolders => $folder) {
        $files = $this->files($folder);

        foreach ($files as $keyFile => $file) {
            $existing_array = array('a'=>'b', 'b'=>'c');
            array_push($files_list,$folder.DIRECTORY_SEPARATOR.$file,);
        }
        }

        // files list
        return $files_list;
    }

    /**
     * List dirs
     * 
     */
    protected function listdirs($dir) {
        static $alldirs = array();
        $dirs = glob($dir . '/*', GLOB_ONLYDIR);
        if (count($dirs) > 0) {
            foreach ($dirs as $d) $alldirs[] = $d;
        }
        foreach ($dirs as $dir) $this->listdirs($dir);
        return $alldirs;
    }

    /**
     * Unlinkr
     * 
     */
    protected function list_directory($path, $action='files') {
        $ListFiles = array();
        $Files = array();
        $Folders = array();

        $scannedFiles  = array_diff(scandir($path), ['.', '..']);
        $ListFiles = array_merge($scannedFiles, $ListFiles);

        foreach($scannedFiles as $file)
        {
            if( is_file($path.DIRECTORY_SEPARATOR.$file) )
            {
                array_push($Files, $file);
            }else{
                array_push($Folders, $file);
            }
        }

        return (($action == 'files') ? $Files : $Folders);
    }

    /**
     * Unlinkr
     *
     * @param string $path
     * @return mixed
     */
    protected function unlinkr($dir, $pattern = "*") {
        // find all files and folders matching pattern
        $files = glob($dir . "/$pattern"); 
    
        //interate thorugh the files and folders
        foreach($files as $file){ 
        //if it is a directory then re-call unlinkr function to delete files inside this directory     
            if (is_dir($file) and !in_array($file, array('..', '.')))  {
                //echo "<p>opening directory $file </p>";
                $this->unlinkr($file, $pattern);
                //remove the directory itself
                //echo "<p> deleting directory $file </p>";
                rmdir($file);
            } else if(is_file($file) and ($file != __FILE__)) {
                // make sure you don't delete the current script
                //echo "<p>deleting file $file </p>";
                unlink($file); 
            }
        }
    }

   /**
     * Copy directory
     *
     */
    protected function copy_directory($src,$dst) {
        $dir = opendir($src);
        if(!is_dir($dst) || !file_exists($dst)){ @mkdir($dst); }
        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if ( is_dir($src . '/' . $file) ) {
                  $this->copy_directory($src . '/' . $file,$dst . '/' . $file);
                }
                else {
                    copy($src . '/' . $file,$dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    /**
    * Get the directory size
    * @param directory $dir
    * @return integer
    */
    protected function dirSize($dir_path) {
        $dir = rtrim(str_replace('\\', '/', $dir_path), '/');

        if (is_dir($dir) === true) {
            $totalSize = 0;
            $os        = strtoupper(substr(PHP_OS, 0, 3));
            // If on a Unix Host (Linux, Mac OS)
            if ($os !== 'WIN') {
                $io = popen('/usr/bin/du -sb ' . $dir, 'r');
                if ($io !== false) {
                    $totalSize = intval(fgets($io, 80));
                    pclose($io);
                    return $totalSize;
                }
            }
            // If on a Windows Host (WIN32, WINNT, Windows)
            if ($os === 'WIN' && extension_loaded('com_dotnet')) {
                $obj = new \COM('scripting.filesystemobject');
                if (is_object($obj)) {
                    $ref       = $obj->getfolder($dir);
                    $totalSize = $ref->size;
                    $obj       = null;
                    return $totalSize;
                }
            }
            // If System calls did't work, use slower PHP 5
            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir));
            foreach ($files as $file) {
                $totalSize += $file->getSize();
            }
            return $totalSize;
        } else if (is_file($dir) === true) {
            return filesize($dir);
        }
    } 
    
}