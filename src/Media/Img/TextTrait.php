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
| Text Trait
|---------------------------------------------------
*/
trait TextTrait
{

    /*
    * Font file
    */
    public $font = null;

    /*
    * Font size
    */
    public $size = 14;

    /*
    * Set default font
    */
    public function noFont() {
        // Set
        $this->font = null;

        return clone($this);
    }

    /*
    * Font
    * Set font
    */
    public function font($value) {
        return $this->font($value);
    }

    /*
    * Get font
    */
    public function getFont() {
        return $this->font;
    }

    /*
    * Set font
    */
    public function setFont($value) {
        $this->font = $value;

        return clone($this);
    }

    /*
    * Size
    * Set size
    */
    public function size($value) {
        return $this->size($value);
    }

    /*
    * Get size
    */
    public function getSize() {
        return $this->size;
    }

    /*
    * Set size
    */
    public function setSize($value) {
        $this->size = $value;

        return clone($this);
    }

    /*
    * Text
    */
    public function text($x, $y, $string, $angle=0) {
        if(is_null($this->font)) {
            imagestring($this->image, $this->size, $x, $y, $string, $this->color);
        }else{
            imagettftext($this->image, $this->size, $angle, $x, $y, $this->color, $this->font, $string);
        }
        
        return clone($this);
    }

}