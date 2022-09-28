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
    
    /*
    * Global traits
    *
    */
    use \Kiaan\Application\Resources\Global\ExtendingTrait;
     
    /**
     * Page 
     * 
     * Render page
     */
    public function page($path, $data=[]) {
        // Global
        if(!empty($this->dataGlobal)){
            if(isset($this->dataGlobal['*'])){
                $global[] = $this->dataGlobal['*'];
            }

            if(isset($this->dataGlobal[$path])){
                $global[] = $this->dataGlobal[$path];
            }

            $pathGlobal = explode('.', $path);
            if(count($pathGlobal) > 1){
                array_pop($pathGlobal);
                $pathGlobal = implode('.', $pathGlobal);

                if(isset($this->dataGlobal["$pathGlobal.*"])){
                    $global[] = $this->dataGlobal["$pathGlobal.*"];
                }
            }
            
            $my_global = array();
            foreach($global as $global){
                $my_global = array_merge($my_global, $global);
            }
        }else{
            $my_global = array();
        }

        // Data
        $data = array_merge($this->data, $my_global, $data);
        $this->data = array();
        
        // Render
        echo $this->clean();
        echo $this->run($path, $data);
        die();
    }

    /**
     * Render 
     * 
     * Render string
     */
    public function render($path, $data=[]) {
        // Global
        if(isset($this->dataGlobal['*'])){
            $global = $this->dataGlobal['*'];
        }else{
            $global = array();
        }

        // Data
        $data = array_merge($this->data, $global, $data);
        $this->data = array();

        // Render
        echo $this->clean();
        echo $this->run($path, $data);
        die();
    }

    /**
     * HTML page
     * 
     * Get html code for page
     */
    public function html($path, $data=[]) {
        // Global
        if(isset($this->dataGlobal['*'])){
            $global = $this->dataGlobal['*'];
        }else{
            $global = array();
        }

        // Data
        $data = array_merge($this->data, $global, $data);
        $this->data = array();

        // Render
        $this->clean();
        $content = $this->run($path, $data);
        return $this->runString($content, $data);
    }

    /**
     * Data
     * 
     */
    public function data($variables, $value = null) {
        if (is_array($variables)) {
            $this->data = array_merge($variables, $this->data);
        } else {
            $this->data = array_merge([$variables => $value], $this->data);
        }

        return clone($this);
    }

    /**
     * Returns true if the template exists. Otherwise it returns false
     *
     * @param $templateName
     * @return bool
     */
    public function exists($templateName)
    {
        $file = $this->getTemplateFile($templateName);
        return file_exists($file);
    }

    /**
     * Directive
     * 
     * Register a handler for custom directives, helper function & fillter.
     * @param string $name
     * @param callable $handler
     * @return void
     */
    public function directive($name, callable $handler)
    {
        $this->directiveRT($name, $handler);
    }

    /**
     * Adds a global variable. If <b>$varname</b> is an array then it merges all the values.
     * <b>Example:</b>
     * <pre>
     * $this->global('variable',10.5);
     * $this->global('variable2','hello');
     * // or we could add the two variables as:
     * $this->global(['variable'=>10.5,'variable2'=>'hello']);
     * </pre>
     *
     * @param string|array $varname It is the name of the variable or it is an associative array
     * @param mixed $value
     * @return $this
     */
    public function global($varname, $value = null, $target = '*')
    {
        if (is_array($varname)) {
            // Target
            $target = str_replace(["/", "\\"], ".", (is_null($value) ? '*' : trim(trim($value, '/'))));

            // Value
            $value = $varname;
        } else {
            // Target
            $target = str_replace(["/", "\\"], ".", trim(trim($target, '/')));

            // Value
            $value = [$varname => $value];
        }

        // Set
        $this->dataGlobal[$target] = $value;
        $this->variablesGlobal[$target] = $value;
        
        return clone($this);
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