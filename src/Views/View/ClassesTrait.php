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
| Classes Trait
|---------------------------------------------------
*/
trait ClassesTrait {

    /**
     * Get alias classes
    */
    public function getClasses()
    {
        return $this->aliasClasses;
    }

    /**
     * Set alias classes
    */
    public function setClasses(array $list)
    {
        $list = array_map('trim', $list);
        return $this->aliasClasses = str_replace(['/', '//', '.'], '\\', $list);
    }

    /**
     * Add alias classes 
    */
    public function classes(array $list)
    {
        $list = array_map('trim', $list);
        return $this->aliasClasses = str_replace(['/', '//', '.'], '\\', array_merge($this->aliasClasses, $list));
    }

}