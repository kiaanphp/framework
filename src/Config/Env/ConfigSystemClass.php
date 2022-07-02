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
namespace Kiaan\Config\Env;

/*
|---------------------------------------------------
| Config system Class
|---------------------------------------------------
*/
class ConfigSystemClass
{

    /**
     * Comment symbol
     * 
    */
    protected $commentSymbol = "#";

    /**
     * Get commentSymbol
     * 
    */
    public function getCommentSymbol() {
        return $this->commentSymbol;
    }

    /**
     * Set  commentSymbol
     * 
    */
    public function setCommentSymbol($value) {
        return $this->commentSymbol = $value;
    }

}

