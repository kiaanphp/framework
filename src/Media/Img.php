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
namespace Kiaan\Media;

/*
|---------------------------------------------------
| Uses
|---------------------------------------------------
*/
use GdImage;

/*
|---------------------------------------------------
| Image
|---------------------------------------------------
*/
class Img {

    /**
     *  Traits
     * 
    */
    use Img\LoaderTrait;
    use Img\SaveTrait;
    use Img\ColorTrait;
    use Img\DrawTrait;
    use Img\TextTrait;
    use Img\FilterTrait;
    use Img\HelperTrait;
    
    /*
    * Global traits
    *
    */
    use \Kiaan\Application\Resources\Global\ExtendingTrait;
    use \Kiaan\Application\Resources\Global\FilesystemPathTrait;
    
    /*
    * Resize
    */
    public function resize($width, $height) {
        // Canvas
        $canvas = imagecreatetruecolor($width, $height);

        // fill with transparent color
        $this->truecolorTransparent($canvas);
        
        // Resized
        imagecopyresized($canvas, $this->image, 0, 0, 0, 0, $width,$height, imagesx($this->image), imagesy($this->image));

        // Return
        $this->image = $canvas;

        return clone($this);
    }

    /*
    * Get width
    */
    public function width() {
      return imagesx($this->image);
  }

    /*
    * Get Height
    */
    public function height() {
      return imagesy($this->image);
  }
  
    /*
    * Rotate
    */
    public function rotate($angle) {
      $transparent = imagecolorallocatealpha($this->image, 255, 255, 255, 127);

      $this->image = imagerotate($this->image, $angle, $transparent);

      return clone($this);
    }

}