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
| Comments Trait
|---------------------------------------------------
*/
trait CommentsTrait {
    
    /**
     * Compile html comments into valid PHP.
     *
     * @param string $value
     * @return string
     */
    protected function compileComments($value)
    {
        $pattern = \sprintf('/%s--(.*?)--%s/s', $this->contentTags[0], $this->contentTags[1]);
        return \preg_replace($pattern, $this->phpTag . '/*$1*/ ?>', $value);
    }

}

