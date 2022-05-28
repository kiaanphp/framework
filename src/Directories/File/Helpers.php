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
namespace Kiaan\Directories\File;

/*
|---------------------------------------------------
| Helpers
|---------------------------------------------------
*/
trait Helpers {

    /*
    * Extract
    */
    protected function extract($file, $type='namespace')
    {
        $file = $this->preparePathFileSystem($file);

        $ns = false;
        $handle = fopen($file, 'r');

        if($handle){
            while(($line=fgets($handle)) !== false){
                if(strpos($line, $type) === 0){
                    $parts = explode(' ', $line);
                    $ns = rtrim(trim($parts[1]), ';');
                    break;
                }
            }
            fclose($handle);
        }
        return $ns;
    }

}