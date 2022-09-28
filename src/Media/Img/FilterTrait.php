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
| Filter Trait
|---------------------------------------------------
*/
trait FilterTrait
{
    /*
    * Emboss
    */
    public function emboss() {
        imagefilter($this->image, IMG_FILTER_EMBOSS);

        return clone($this);
    }

    /*
    * Negate
    */
    public function negate() {
        imagefilter($this->image, IMG_FILTER_NEGATE);
        
        return clone($this);
    }
      
    /*
    * Contrast
    */
    public function contrast($value) {
        imagefilter($this->image, IMG_FILTER_CONTRAST, $value);
        
        return clone($this);
    }

    /*
    * Edgedetect
    */
    public function edgedetect() {
        imagefilter($this->image, IMG_FILTER_EDGEDETECT);
        
        return clone($this);
    }

    /*
    * Grayscale
    */
    public function grayscale() {
        imagefilter($this->image, IMG_FILTER_GRAYSCALE);
        
        return clone($this);
    }

    /*
    * Mean removal
    */
    public function meanRemoval() {
        imagefilter($this->image, IMG_FILTER_MEAN_REMOVAL);
        
        return clone($this);
    }

    /*
    * Colorize
    */
    public function colorize($r, $g, $b, $a=0) {
        imagefilter($this->image, IMG_FILTER_COLORIZE, $r, $g, $b ,$a);
        
        return clone($this);
    }

    /*
    * Pixelate
    */
    public function pixelate($value) {
        imagefilter($this->image, IMG_FILTER_PIXELATE, $value);
        
        return clone($this);
    }

    /*
    * Brightness
    */
    public function brightness($value) {
        imagefilter($this->image, IMG_FILTER_BRIGHTNESS, $value);
  
        return clone($this);
    }

}