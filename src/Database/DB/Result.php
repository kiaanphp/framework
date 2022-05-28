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
| Result
|---------------------------------------------------
*/
class Result implements \IteratorAggregate
{

    /**
    * Traits
    *
    */
	use RelationshipsTrait;

	/**
	 * DB
	*/
	protected $db;	

	/**
	 * Data
	*/
	protected $data;

	/**
	 * Count
	*/
	public $count;

	/**
	 * page
	*/
	public $page;

    /*
    * Construct
    *
    */
    public function __construct($db, $data, $primary_key='id', $page='', $count='')
    {
        $this->db = $db;
        $this->data = $data;
		$this->primary_key = $primary_key;
		$this->page = $page;
		$this->count = $count;
	}

    /*
    * Magic functions
    *
    */
    function __get($key){
        return $this->data->$key;
    }
    
    public function __set($key, $value) {
        return $this->data->$key = $value;
    }

	/**
	 * Returns an iterator for the elements.
	 *
	 * @return \Iterator Over map elements
	 */
	public function getIterator() : \Iterator
	{
		return new \ArrayIterator( $this->data );
	}

	/**
	 * Returns an element at a given offset.
	 * 
	 * @param mixed $key Key to return the element for
	 * @return mixed Value associated to the given key
	 */
	public function offsetGet( $key )
	{
		return $this->data[$key] ?? null;
	}

	/**
	 * Count
	 *
	 * Get count of results
	 */
	public function count()
	{
		return $this->count;
	}

	/**
	 * Json
	 *
	 * Return results to json
	 */
	public function json()
	{
		return json_encode($this->data);
	}

}