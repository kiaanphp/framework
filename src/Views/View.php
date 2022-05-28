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
namespace Kiaan\Views;

/*
|---------------------------------------------------
| Uses
|---------------------------------------------------
*/
use Kiaan\Views\View\Engine;

/*
|---------------------------------------------------
| View
|---------------------------------------------------
*/
class View extends Engine {

    /**
     * Page 
     * Render page
     */
    public function page($path, $data=[]) {
        echo $this->clean();
        echo $this->run($path, $data);
        die();
    }

    /**
     * Render 
     * Render string
     */
    public function render($path, $data=[]) {
        echo $this->clean();
        echo $this->runString($path, $data);
        die();
    }

    /**
     * HTML page
     * Get html code for page
     */
    public function html($path, $data=[]) {
        $this->clean();
        $content = $this->run($path, $data);
        return $this->runString($content, $data);
    }

    /**
     * Returns true if the template exists. Otherwise it returns false
     *
     * @param $templateName
     * @return bool
     */
    public function exist($templateName)
    {
        $file = $this->getTemplateFile($templateName);
        return \file_exists($file);
    }

    /**
     * Register a handler for custom directives, helper function & fillter.
     *
     * @param string $name
     * @param callable $handler
     * @return void
     */
    public function helper($name, callable $handler)
    {
        $this->directiveRT($name, $handler);
    }

    /**
     * Adds a global variable. If <b>$varname</b> is an array then it merges all the values.
     * <b>Example:</b>
     * <pre>
     * $this->share('variable',10.5);
     * $this->share('variable2','hello');
     * // or we could add the two variables as:
     * $this->share(['variable'=>10.5,'variable2'=>'hello']);
     * </pre>
     *
     * @param string|array $varname It is the name of the variable or it is an associative array
     * @param mixed $value
     * @return $this
     */
    public function share($varname, $value = null)
    {
        if (is_array($varname)) {
            $this->variablesGlobal = \array_merge($this->variablesGlobal, $varname);
        } else {
            $this->variablesGlobal[$varname] = $value;
        }
        return $this;
    }

    /**
    * Get SCRF(input, value)
    */
    public function getCsrf()
    {
        return $this->csrf;
    }

    /**
     * Set SCRF(input, value)
    */
    public function setCsrf($input, $value)
    {
        $this->csrf = ["input" => $input, "value" => $value];
    }

    /**
     * Get the file extension for template files.
     *
     * @return string
     */
    public function getFileExtension()
    {
        return $this->fileExtension;
    }

    /**
     * Set the file extension for the template files.
     * It must includes the leading dot e.g. .html.php
     *
     * @param string $fileExtension Example: .prefix.ext
     */
    public function setFileExtension($fileExtension)
    {
        $this->fileExtension = $fileExtension;
    }

    /**
    * Get method
    * Get method input name
    */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Set method
     * Set method input name
    */
    public function setMethod($name)
    {
        $this->method = $name;
    }

    /**
    * Get roots path
    */
    public function getRootsPath()
    {
        return $this->rootsPath;
    }

    /**
     * Set roots path
    */
    public function setRootsPath($root, $public)
    {
        return $this->rootsPath = ["root" => $root, "public" => $public, "asset" => $public];
    }

    /**
     * Clean cache
     * @return null
     */
    public function clean()
    {
        $compiledPath = $this->compiledPath;

        // Clean
        $files = glob("$compiledPath/*"); // get all file names
        foreach($files as $file){ // iterate files
        if(is_file($file)) {
            unlink($file); // delete file
        }
        }
    }

}