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
| Draw Trait
|---------------------------------------------------
*/
trait DrawTrait
{

    /*
    * Thickness
    */
    public $thickness = 1;

    /*
    * Line
    */
    public function line($x1, $y1, $x2, $y2) {
        imageline($this->image, $x1, $y1, $x2, $y2, $this->color);
        
        return clone($this);
    }

    /*
    * Thickness
    */
    public function thickness($value) {
        // Get
        if (empty($value)) {return $this->thickness;}

        // Set
        imagesetthickness($this->image, $value);

        return clone($this);
    }

    /*
    * Rectangle
    */
    public function rectangle($x1, $y1, $x2, $y2) {
        imagerectangle($this->image, $x1, $y1, $x2, $y2, $this->color);
        
        return clone($this);
    }

    /*
    * Filled rectangle
    */
    public function filledRectangle($x1, $y1, $x2, $y2) {
        imagefilledrectangle($this->image, $x1, $y1, $x2, $y2, $this->color);
        
        return clone($this);
    }

    /*
    * Ellipse
    */
    public function ellipse($x1, $y1, $x2, $y2) {
        imageellipse($this->image, $x1, $y1, $x2, $y2, $this->color);
        
        return clone($this);
    }

    /*
    * Filled ellipse
    */
    public function filledEllipse($x1, $y1, $x2, $y2) {
        imagefilledellipse($this->image, $x1, $y1, $x2, $y2, $this->color);
        
        return clone($this);
    }

}