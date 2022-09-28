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
namespace Kiaan\Database\DB;

/*
|---------------------------------------------------
| Result trait
|---------------------------------------------------
*/
trait ResultTrait {

    /**
    * Traits
    *
    */
	use RelationshipsTrait;

	/**
	 * Returns an iterator for the elements.
	 *
	 * @return \Iterator Over map elements
	 */
	public function getIterator() : \Iterator
	{
		return new \ArrayIterator($this->data);
	}

	/**
	 * Returns an element at a given offset.
	 * 
	 * @param mixed $key Key to return the element for
	 * @return mixed Value associated to the given key
	 */
    public function __get($key)
    {
        return $this->data->{$key};
    }

	/*
	* Set magic function.
	*/
	/*
	public function __set($key, $value)
    {
		if(isset($this->data->{$key})){
			$this->data->{$key} = $value;
		}else{
			$this->data[$key] = $value;
		}
    }
	*/

    /**
    * Returns data object
    *
    */
    public function data()
    {
        return $this->data;
    }

    /**
    * Value
    *
    */
    public function value($column)
    {
		if(is_array($this->data)){
			return array_column($this->data, $column);
		}else{
			return $this->data->{$column};
		}
        return $this->data;
    }

	/**
	 * Get count of results
	 */
	public function length()
	{
		if(!is_array($this->data)){
			return (empty($this->data)) ? 0 : 1;
		}

		return count($this->data);
	}

	/**
	 * Return results to json
	 */
	public function toJson()
	{
		return $this->data;
	}

	/**
	 * Return results to json
	 */
	public function toArray()
	{
		return json_decode($this->data);
	}

}