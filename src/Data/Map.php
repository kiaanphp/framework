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
namespace Kiaan\Data;

/*
|---------------------------------------------------
| Uses
|---------------------------------------------------
*/
use Countable;
use ArrayAccess;
use ArrayIterator;
use CachingIterator;
use JsonSerializable;
use IteratorAggregate;

/*
|---------------------------------------------------
| Map
|---------------------------------------------------
*/
class Map {

    /**
    * Traits
    *
    */
    use Map\ResponseTrait;
    use Map\DataTrait;
    use Map\ReturnsTrait;
    use Map\MethodsTrait;
    use Map\CommandsTrait;
    use Map\HelpersTrait;

    /*
    * List
    *
    */
    protected $list = array();

    /*
    * __get && __set
    *
    */
    public function __get($name) {
        return $this->list[$name];
    }

    public function __set($name, $value) {
        $this->list[$name] = $value;
        return $this;
    }
    
}