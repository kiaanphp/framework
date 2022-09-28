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
| Color Trait
|---------------------------------------------------
*/
trait ColorTrait
{

    /*
    * Color
    */
    protected $color;

    /*
    * Color
    */
    public function color($r, $g, $b, $a="") {
        $this->color = (empty($a)) ? imagecolorallocate($this->image, $r, $g, $b) : imagecolorallocatealpha($this->image, $r, $g, $b, $a);
        
        return clone($this);
    }

    /*
    * Fill
    */
    public function fill($x, $y) {
        imagefill($this->image, $x, $y, $this->color);

        return clone($this);
    }

    /*
    * Picker
    */
    public function picker($x, $y) {
        $picker = imagecolorat($this->image, $x, $y);

        $red = ($picker >> 16) & 0xff;
        $green = ($picker >> 8) & 0xff;
        $blue = $picker & 0xff;

        return ["red" => $red, "green" => $green, "blue" => $blue];
    }

}