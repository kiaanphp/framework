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
namespace Kiaan\Data\Crawl;

/*
|---------------------------------------------------
| Class
|---------------------------------------------------
*/
class NodeList extends \ArrayObject
{

    /**
     * Returns the item at the specified index.
     * 
     */
    public function item(int $index)
    {
        return $this->offsetExists($index) ? $this->offsetGet($index) : null;
    }

    /**
     * Returns the value for the property specified.
     * 
     */
    public function __get(string $name)
    {
        if ($name === 'length') {
            return sizeof($this);
        }
        throw new \Exception('Undefined property: ' . $name);
    }
}
