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
namespace Kiaan\Data\Collection;

/*
|---------------------------------------------------
| Response trait
|---------------------------------------------------
*/
trait ResponseTrait {

    /**
	 * Returns a plain array of the given elements.
     * 
	*/
    public function toArray($items=null)
    {
        if (!empty($items) && !is_array($items)) {
            // Is json
            if (is_object(@json_decode($items)))
            { 
                return json_decode($items, true);
            }
            // Is object
            elseif(is_object($items)){
                return json_decode(json_encode($items), true);
            }
        }

        $item = (empty($items)) ? $item = $this->list : $item = $items;
        return json_decode(json_encode($item), true);
    }

    /**
	 * Returns a object of the given elements.
     * 
	*/
    public function toObject()
    {
        $result = json_decode(json_encode($this->list), false);
        return is_object($result) ? $result : null;
    }

    /**
	 * Returns a json of the given elements.
     * 
	*/
    public function toJson()
    {
       return json_encode($this->list);
    }
    
}