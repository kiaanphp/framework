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
| Helper Trait
|---------------------------------------------------
*/
trait HelperTrait
{

    /*
    * Save
    * Save image to file
    */
    protected function saveToFile($path, $extension='jpeg') {
        ob_start();

        // Path
        $path = $this->preparePathFileSystem($path);

        // Save by extension
        switch (strtolower($extension)) {
            case 'png':
                imagepng($this->image, $path, 1);   
                break;

            case 'jpeg':
                $image = imagejpeg($this->image, $path, $this->quality);  
                break;

            case 'gif':
                imagegif($this->image, $path, $this->quality);   
                break;

            case 'bmp':
                imagebmp($this->image, $path, $this->quality);   
                break;

            case 'webp':
                imagewebp($this->image, $path, $this->quality);   
                break;

            default:
            throw new \Exception("Unsupported image type. GD driver is only able to decode JPG, PNG, GIF, BMP or WebP files.");
            }

        // Clear data
        $this->clearData();

        return $this->image;
    }

    /**
     * Clear data
     *
     * @return string
     */
    protected function clearData()
    {
        // Clear data
        $this->image = null;
        $this->type = "jpeg";

        // Clean content
        $buffer = ob_get_contents();
        ob_end_clean();

        return $buffer;   
    }

    /**
     * Initiates new image from path in filesystem
     *
     */
    protected function initFromPath($path)
    {
        // get mime type of file
        $mime = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $path);

        // define core
        switch (strtolower($mime)) {
            case 'image/png':
            case 'image/x-png':
                $core = @imagecreatefrompng($path);
                $this->type = 'png';
                break;

            case 'image/jpg':
            case 'image/jpeg':
            case 'image/pjpeg':
                $core = @imagecreatefromjpeg($path);
                if (!$core) {
                    $core= @imagecreatefromstring(file_get_contents($path));
                }
                $this->type = 'jpeg';
                break;

            case 'image/gif':
                $core = @imagecreatefromgif($path);
                $this->type = 'gif';
                break;

            case 'image/bmp':
                $core = @imagecreatefromwbmp($path);
                $this->type = 'bmp';
                break;

            case 'image/webp':
            case 'image/x-webp':
                if (!function_exists('imagecreatefromwebp')) {
                    throw new \Exception("Unsupported image type. GD/PHP installation does not support WebP format.");
                }
                $core = @imagecreatefromwebp($path);
                $this->type = 'webp';
                break;

            default:
                throw new \Exception("Unsupported image type. GD driver is only able to decode JPG, PNG, GIF, BMP or WebP files.");
        }

        if (empty($core)) {throw new \Exception("Unable to decode image from file ({$path}).");}

        // build image
        $this->image = $core;
        $this->mime = mime_content_type($path);

        imagedestroy($core);
        imagedestroy($this->image);

        return $core;
    }

    /**
     * Initiates new image from binary data
     *
     */
    protected function initFromBinary($binary)
    {
        $image = @imagecreatefromstring($binary);

        if ($image === false) {throw new \Exception("Unable to init from given binary data.");}
        
        $this->image = $image;

        imagedestroy($image);
        imagedestroy($this->image);

        return $image;
    }

    /**
     * Truecolor transparent
    */
    protected function truecolorTransparent($canvas)
    {
        $width = imagesx($canvas);
        $height = imagesy($canvas);
        
        imagealphablending($canvas, false);
        $transparent = imagecolorallocatealpha($canvas, 255, 255, 255, 127);
        imagefilledrectangle($canvas, 0, 0, $width, $height, $transparent);
        imagecolortransparent($canvas, $transparent);
        imagealphablending($canvas, true);
    }

}