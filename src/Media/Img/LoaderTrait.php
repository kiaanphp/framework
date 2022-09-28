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
namespace Kiaan\Media\Img;

/*
|---------------------------------------------------
| Uses
|---------------------------------------------------
*/
use GdImage;

/*
|---------------------------------------------------
| Loader Trait
|---------------------------------------------------
*/
trait LoaderTrait
{

    /**
     *  Root path
     * 
    */
    protected $root_path;

    /**
     *  Image
     * 
    */
    protected $image;

    /**
     *  Mime
     * 
    */
    protected $mime;

    /**
     *  Load Image from (file or string)
     * 
    */
    public function load($path) {
        $path_file = $this->filesystemRoot() . $path;

        if (is_file($path_file)) {
          // File
          $image = $this->initFromPath($path_file);
        }else{
          // string
          $image = $this->initFromBinary($path);
        }
  
        $this->image = $image;
  
        return clone($this);
    }

    /*
    * Load
    */
    public function image($path='') {
        return $this->load($path);
    }

    /*
    * Create
    */
    public function create($width, $height) {
        // New canvas
        $canvas = imagecreatetruecolor($width, $height);

        // Transparent 
        $this->truecolorTransparent($canvas);

        $this->image = $canvas;
        imagedestroy($canvas);
        imagedestroy($this->image);

        return clone($this);
      }

}