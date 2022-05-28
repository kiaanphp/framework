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
| Inheritance Trait
|---------------------------------------------------
*/
trait InheritanceTrait {

    /**
     * Compile the content statements into valid PHP.
     *
     * @param string $expression
     * @return string
     */
    protected function compileContent($expression)
    {
        return $this->phpTagEcho . "\$this->contentContent{$expression}; ?>";
    }

    /**
     * Compile the show statements into valid PHP.
     *
     * @return string
     */
    protected function compileShow()
    {
        return $this->phpTagEcho . '$this->contentSection(); ?>';
    }

    /**
     * Compile the section statements into valid PHP.
     *
     * @param string $expression
     * @return string
     */
    protected function compileSection($expression)
    {
        return $this->phpTag . "\$this->startSection{$expression}; ?>";
    }

    /**
     * Compile the append statements into valid PHP.
     *
     * @return string
     */
    protected function compileAppend()
    {
        return $this->phpTag . '$this->appendSection(); ?>';
    }

    /**
     * Compile the end-section statements into valid PHP.
     *
     * @return string
     */
    protected function compileEndsection()
    {
        return $this->phpTag . '$this->stopSection(); ?>';
    }

    /**
     * Compile the extends statements into valid PHP.
     *
     * @param string $expression
     * @return string
     */
    protected function compileExtends($expression)
    {
        $expression = $this->stripParentheses($expression);
        // $_shouldextend avoids to runchild if it's not evaluated.
        // For example @if(something) @extends('aaa.bb') @endif()
        // If something is false then it's not rendered at the end (footer) of the script.
        $this->uidCounter++;
        $data = $this->phpTag . 'if (isset($_shouldextend[' . $this->uidCounter . '])) { echo $this->runChild(' . $expression . '); } ?>';
        $this->footer[] = $data;
        return $this->phpTag . '$_shouldextend[' . $this->uidCounter . ']=1; ?>';
    }

    /**
     * Execute the @parent command. This operation works in tandem with extendSection
     *
     * @return string
     * @see extendSection
     */
    protected function compileParent()
    {
        return $this->PARENTKEY;
    }

    /**
     * Compile the include statements into valid PHP.
     *
     * @param string $expression
     * @return string
     */
    protected function compileInclude($expression)
    {
        $expression = $this->stripParentheses($expression);
        return $this->phpTagEcho . '$this->runChild(' . $expression . '); ?>';
    }
    
    /**
     * It loads an compiled template and paste inside the code.<br>
     * It uses more disk space but it decreases the number of includes<br>
     *
     * @param $expression
     * @return string
     * @throws Exception
     */
    protected function compileIncludeFast($expression)
    {
        $expression = $this->stripParentheses($expression);
        $ex = $this->stripParentheses($expression);
        $exp = \explode(',', $ex);
        $file = $this->stripQuotes(isset($exp[0]) ? $exp[0] : null);
        $fileC = $this->getCompiledFile($file);
        if (!@\file_exists($fileC)) {
            // if the file doesn't exist then it's created
            $this->compile($file, true);
        }
        return $this->getFile($fileC);
    }

    /**
     * Compile the include statements into valid PHP.
     *
     * @param string $expression
     * @return string
     */
    protected function compileIncludeIf($expression)
    {
        return $this->phpTag . 'if ($this->templateExist' . $expression . ') echo $this->runChild' . $expression . '; ?>';
    }

    /**
     * Compile the include statements into valid PHP.
     *
     * @param string $expression
     * @return string
     */
    protected function compileIncludeWhen($expression)
    {
        $expression = $this->stripParentheses($expression);
        return $this->phpTagEcho . '$this->includeWhen(' . $expression . '); ?>';
    }

    /**
     * Compile the includefirst statement
     *
     * @param string $expression
     * @return string
     */
    protected function compileIncludeFirst($expression)
    {
        $expression = $this->stripParentheses($expression);
        return $this->phpTagEcho . '$this->includeFirst(' . $expression . '); ?>';
    }

    
    /**
     * Compile the stack statements into the content.
     *
     * @param string $expression
     * @return string
     */
    protected function compileStack($expression)
    {
        return $this->phpTagEcho . "\$this->contentPushContent{$expression}; ?>";
    }
    /**
     * Compile the push statements into valid PHP.
     *
     * @param string $expression
     * @return string
     */
    public function compilePush($expression)
    {
        return $this->phpTag . "\$this->startPush{$expression}; ?>";
    }

    /**
     * Compile the push statements into valid PHP.
     *
     * @param string $expression
     * @return string
     */
    public function compilePushOnce($expression)
    {
        $key = '$__pushonce__' . \trim(\substr($expression, 2, -2));
        return $this->phpTag . "if(!isset($key)): $key=1;  \$this->startPush{$expression}; ?>";
    }

    /**
     * Compile the push statements into valid PHP.
     *
     * @param string $expression
     * @return string
     */
    public function compilePrepend($expression)
    {
        return $this->phpTag . "\$this->startPush{$expression}; ?>";
    }

    /**
     * Compile the endpush statements into valid PHP.
     *
     * @return string
     */
    protected function compileEndpush()
    {
        return $this->phpTag . '$this->stopPush(); ?>';
    }

    /**
     * Compile the endpushonce statements into valid PHP.
     *
     * @return string
     */
    protected function compileEndpushOnce()
    {
        return $this->phpTag . '$this->stopPush(); endif; ?>';
    }

    /**
     * Compile the endpush statements into valid PHP.
     *
     * @return string
     */
    protected function compileEndPrepend()
    {
        return $this->phpTag . '$this->stopPrepend(); ?>';
    }

    /**
     * Compile the component statements into valid PHP.
     *
     * @param string $expression
     * @return string
     */
    protected function compileComponent($expression)
    {
        return $this->phpTag . " \$this->startComponent{$expression}; ?>";
    }

    /**
     * Compile the end-component statements into valid PHP.
     *
     * @return string
     */
    protected function compileEndComponent()
    {
        return $this->phpTagEcho . '$this->renderComponent(); ?>';
    }

    /**
     * Compile the slot statements into valid PHP.
     *
     * @param string $expression
     * @return string
     */
    protected function compileSlot($expression)
    {
        return $this->phpTag . " \$this->slot{$expression}; ?>";
    }

    /**
     * Compile the end-slot statements into valid PHP.
     *
     * @return string
     */
    protected function compileEndSlot()
    {
        return $this->phpTag . ' $this->endSlot(); ?>';
    }

    /**
     * Compile the has section statements into valid PHP.
     *
     * @param string $expression
     * @return string
     */
    protected function compileHasSection($expression)
    {
        return $this->phpTag . "if (! empty(trim(\$this->contentContent{$expression}))): ?>";
    }

    /**
     * Compile the stop statements into valid PHP.
     *
     * @return string
     */
    protected function compileStop()
    {
        return $this->phpTag . '$this->stopSection(); ?>';
    }

    /**
     * Compile the overwrite statements into valid PHP.
     *
     * @return string
     */
    protected function compileOverwrite()
    {
        return $this->phpTag . '$this->stopSection(true); ?>';
    }
    
}

