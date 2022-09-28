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
| Save Trait
|---------------------------------------------------
*/
trait SaveTrait
{

   /**
     *  Quality
     * 
    */
    protected $quality = 75;

   /**
     *  Type
     * 
    */
    protected $type = 'jpeg';

    /*
    * Get quality
    */
    public function getQuality() {
        return $this->quality;
    }

    /*
    * Set quality
    */
    public function setQuality($quality) {
        $this->quality = $quality;
  
        return clone($this);
    }

    /*
    * Quality
    * Set quality
    */
    public function quality($quality) {
        return $this->setQuality($quality);
    }

    /**
     * Png
     *
     */
    public function png($file=null)
    {
        // Save to file(file!=null) or browse image(file=null)
        return $this->saveToFile($file, "png");
    }

    /**
     * Jpg
     *
     */
    public function jpg($file=null)
    {
        // Save to file(file!=null) or browse image(file=null)
        return $this->saveToFile($file, "jpeg");
    }

    /**
     * Gif
     *
     */
    public function gif($file=null)
    {
        // Save to file(file!=null) or browse image(file=null)
        return $this->saveToFile($file, "gif");
    }

    /**
     * Bmp
     *
     */
    public function bmp($file=null)
    {
        // Save to file(file!=null) or browse image(file=null)
        return $this->saveToFile($file, "bmp");
    }

    /**
     * Webp
     *
     */
    public function webp($file=null)
    {
        // Save to file(file!=null) or browse image(file=null)
        return $this->saveToFile($file, "webp");
    }

}