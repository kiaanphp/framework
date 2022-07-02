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
| Statements Trait
|---------------------------------------------------
*/
trait StatementsTrait {

    protected function compileSwitch($expression)
    {
        $this->switchCount++;
        $this->firstCaseInSwitch = true;
        return $this->phpTag . "switch $expression {";
    }

    /**
     * Execute the case tag.
     *
     * @param $expression
     * @return string
     */
    protected function compileCase($expression)
    {
        if ($this->firstCaseInSwitch) {
            $this->firstCaseInSwitch = false;
            return 'case ' . $expression . ': ?>';
        }
        return $this->phpTag . "case $expression: ?>";
    }

    /**
     * Compile the while statements into valid PHP.
     *
     * @param string $expression
     * @return string
     */
    protected function compileWhile($expression)
    {
        return $this->phpTag . "while{$expression}: ?>";
    }

    /**
     * default tag used for switch/case
     *
     * @return string
     */
    protected function compileDefault()
    {
        if ($this->firstCaseInSwitch) {
            return $this->showError('@default', '@switch without any @case', true);
        }
        return $this->phpTag . 'default: ?>';
    }

    protected function compileEndSwitch()
    {
        --$this->switchCount;
        if ($this->switchCount < 0) {
            return $this->showError('@endswitch', 'Missing @switch', true);
        }
        return $this->phpTag . '} // end switch ?>';
    }

    /**
     * Compile the else statements into valid PHP.
     *
     * @return string
     */
    protected function compileElse()
    {
        return $this->phpTag . 'else: ?>';
    }

    /**
     * Compile the for statements into valid PHP.
     *
     * @param string $expression
     * @return string
     */
    protected function compileFor($expression)
    {
        return $this->phpTag . "for{$expression}: ?>";
    }

    /**
     * Compile the foreach statements into valid PHP.
     *
     * @param string $expression
     * @return string
     */
    protected function compileForeach($expression)
    {
        //\preg_match('/\( *(.*) * as *([^\)]*)/', $expression, $matches);
        \preg_match('/\( *(.*) * as *([^)]*)/', $expression, $matches);
        $iteratee = \trim($matches[1]);
        $iteration = \trim($matches[2]);
        $initLoop = "\$__currentLoopData = {$iteratee}; \$this->addLoop(\$__currentLoopData);\$this->getFirstLoop();\n";
        $iterateLoop = '$loop = $this->incrementLoopIndices(); ';
        return $this->phpTag . "{$initLoop} foreach(\$__currentLoopData as {$iteration}): {$iterateLoop} ?>";
    }

    /**
     * Compile the break statements into valid PHP.
     *
     * @param string $expression
     * @return string
     */
    protected function compileBreak($expression)
    {
        return $expression ? $this->phpTag . "if{$expression} break; ?>" : $this->phpTag . 'break; ?>';
    }

    /**
     * Compile the continue statements into valid PHP.
     *
     * @param string $expression
     * @return string
     */
    protected function compileContinue($expression)
    {
        return $expression ? $this->phpTag . "if{$expression} continue; ?>" : $this->phpTag . 'continue; ?>';
    }

    /**
     * Compile the forelse statements into valid PHP.
     *
     * @param string $expression
     * @return string
     */
    protected function compileForelse($expression)
    {
        $empty = '$__empty_' . ++$this->forelseCounter;
        return $this->phpTag . "{$empty} = true; foreach{$expression}: {$empty} = false; ?>";
    }

    /**
     * Compile the if statements into valid PHP.
     *
     * @param string $expression
     * @return string
     */
    protected function compileIf($expression)
    {
        return $this->phpTag . "if{$expression}: ?>";
    }

    /**
     * Compile the else-if statements into valid PHP.
     *
     * @param string $expression
     * @return string
     */
    protected function compileElseif($expression)
    {
        return $this->phpTag . "elseif{$expression}: ?>";
    }

    /**
     * Compile the end-while statements into valid PHP.
     *
     * @return string
     */
    protected function compileEndwhile()
    {
        return $this->phpTag . 'endwhile; ?>';
    }

    /**
     * Compile the end-for statements into valid PHP.
     *
     * @return string
     */
    protected function compileEndfor()
    {
        return $this->phpTag . 'endfor; ?>';
    }

    /**
     * Compile the end-for-each statements into valid PHP.
     *
     * @return string
     */
    protected function compileEndforeach()
    {
        return $this->phpTag . 'endforeach; $this->popLoop(); $loop = $this->getFirstLoop(); ?>';
    }

    /**
     * Compile the end-if statements into valid PHP.
     *
     * @return string
     */
    protected function compileEndif()
    {
        return $this->phpTag . 'endif; ?>';
    }

    /**
     * Compile the end-for-else statements into valid PHP.
     *
     * @return string
     */
    protected function compileEndforelse()
    {
        return $this->phpTag . 'endif; ?>';
    }

}

