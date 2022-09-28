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
namespace Kiaan\Views\View;

/*
|---------------------------------------------------
| Functions Trait
|---------------------------------------------------
*/
trait FunctionsTrait {

    /**
     * Compile the raw PHP statements into valid PHP.
     *
     * @param string $expression
     * @return string
     */
    protected function compilePhp($expression)
    {
        return $expression ? $this->phpTag . "{$expression}; ?>" : $this->phpTag . '';
    }

    /**
     * Compile end-php statement into valid PHP.
     *
     * @return string
     */
    protected function compileEndphp()
    {
        return ' ?>';
    } 

    /**
     * Compile the unless statements into valid PHP.
     *
     * @param string $expression
     * @return string
     */
    protected function compileUnless($expression)
    {
        return $this->phpTag . "if ( ! $expression): ?>";
    }

    /**
     * Compile the endunless statements into valid PHP.
     *
     * @return string
     */
    protected function compileEndunless()
    {
        return $this->phpTag . 'endif; ?>';
    }

    /**
     * Compile the unset statements into valid PHP.
     *
     * @param string $expression
     * @return string
     */
    protected function compileUnset($expression)
    {
        return $this->phpTag . "unset{$expression}; ?>";
    }

    /*
    * Isset
    */
    protected function compileIsset($expression)
    {
        return $this->phpTag . "if(isset{$expression}): ?>";
    }

    protected function compileEndIsset()
    {
        return $this->phpTag . 'endif; ?>';
    }
    
    /**
     * Compile the forelse statements into valid PHP.
     *
     * @param string $expression empty if it's inside a for loop.
     * @return string
     */
    protected function compileEmpty($expression = '')
    {
        if ($expression == '') {
            $empty = '$__empty_' . $this->forelseCounter--;
            return $this->phpTag . "endforeach; if ({$empty}): ?>";
        }
        return $this->phpTag . "if (empty{$expression}): ?>";
    }

    /* 
    * end-empty
    */
    protected function compileEndEmpty()
    {
        return $this->phpTag . 'endif; ?>';
    }

    /*
    * Use
    */
    protected function compileUse($expression)
    {
        $value = $this->stripQuotes($this->stripParentheses($expression));

        return $this->phpTag . 'use ' . $value . '; ?>';
    }

    /*
    * CSRF
    */
    protected function compilecsrf()
    {
        $input = $this->csrf['input'];
        $value = $this->csrf['value'];
        return "<input type='hidden' name='$input' value='$value'>";
    }

    /*
    * Method
    */
    protected function compileMethod($expression)
    {
        $name = $this->method;
        $value = $this->stripParentheses($expression);

        return "<input type='hidden' name='$name' value=$value>";
    }

    /*
    * Root
    * Get root path
    */
    protected function compileRoot($expression)
    {
        $root = $this->rootsPath['root'];
        $root = trim(trim($root, "\\"), "/");

        $path = $this->stripQuotes($this->stripParentheses($expression));
        $path = trim(trim($path, "\\"), "/");

        return "$root\\$path";
    }

    /*
    * WWW
    */
    protected function compileWww($expression)
    {
        $path = $this->stripQuotes($this->stripParentheses($expression));
        $path = trim(trim($path, "\\"), "/");

        $protocol = ($_SERVER['REQUEST_SCHEME'] ?? 'http') . '://';
        $host = $_SERVER['HTTP_HOST'] ?? null;
        
        $script_name = str_replace('\\', '', dirname($_SERVER['SCRIPT_NAME']));
        $script_name = str_replace('/public', '', $script_name).'/'.$path;

        $base_url = $protocol . $host . $script_name;
        $base_url = rtrim($base_url, "/");

        return $base_url;
    }

    /*
    * Public
    *
    * Get public url
    */
    protected function compilePublic($expression)
    {
        $path = $this->stripQuotes($this->stripParentheses($expression));
        $path = trim(trim($path, "\\"), "/");
        $public = $this->prepare_path($this->getRootsPath()['public']);

        $protocol = ($_SERVER['REQUEST_SCHEME'] ?? 'http') . '://';
        $host = $_SERVER['HTTP_HOST'] ?? null;
        
        $script_name = str_replace('\\', '', dirname($_SERVER['SCRIPT_NAME']));
        $script_name = str_replace("/$public", '', $script_name)."/$public/".$path;

        $base_url = $protocol . $host . $script_name;
        $base_url = rtrim($base_url, "/");

        return $base_url;
    }

    /*
    * Json
    *
    * Get asset url
    */
    protected function compileJson($array)
    {
        $array = $this->stripParentheses($array);
        return "<?php echo json_encode($array); ?>";
    }

}