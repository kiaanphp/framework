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
namespace Kiaan\Data\Map;

/*
|---------------------------------------------------
| Data trait
|---------------------------------------------------
*/
trait DataTrait {

    /*
    * Construct
    *
    */
    function __construct($items=[]) {
        $this->collect($items);
    }

    /**
     * Collect
     * Collect items to list 
	 * 
	 * Examples:
	 *  Map::collect( ['a', 'b'] );
	 *
     */
    public function collect($items=[])
    {
        $this->list = $items;

        return $this;
    }

	/**
	 * Add an element onto the end of the map without returning a new map.
	 *
	 * Examples:
	 *  Map::from( ['a', 'b'] )->add( 'c' );
	 *  Map::from( ['a' => 'a', 'b' => 'b'] )->add( ['c' => 'c'] );
	 *
	 *
	 * @param mixed $value Value to add to the end
	 * @return self Same map for fluid interface
	 */
	public function add($value)
	{
        if (is_array($value)) {
            $this->list = array_merge($this->list, $value);
        }else{
            $this->list[] = $value;
        }

		return $this;
	}

	/**
     * Gets a value in an array by dot notation for the keys.
     *
	 * #### Example
     * $array = [
     *      'foo' => 'bar',
     *      'baz' => [
     *          'qux => 'foobar'
     *      ]
     * ];
     *
     * Map::collect($array)->get("baz.qux");
     * Map::collect($array)->get("bazr.quxr", 'default');
     * Map::collect($array)->get("baz.qux", function () { return 'email@example.com'; });
     */
    public function get($key, $default=null)
    {
		$array = $this->list;

        if (is_string($key) && is_array($array)) {
            $keys = explode('.', $key);

            while (sizeof($keys) >= 1) {
                $k = array_shift($keys);

                if (!isset($array[$k])) {
                    if(! is_callable($default)){
                        return $default;
                    }else{
                        return call_user_func($default);
                    }
                }

                if (sizeof($keys) === 0) {
                    return $array[$k];
                }

                $array = &$array[$k];
            }
        }

        return $this;
    }

    /**
     * Gets a value in an array by dot notation for the keys.
     *
	 * #### Example
     * $array = [
     *      'foo' => 'bar',
     *      'baz' => [
     *          'qux => 'foobar'
     *      ]
     * ];
     *
     * ->set( 'baz.qux', 'value' );
     */
    public function set($key, $value)
    {
		$array = $this->list;

        if (is_string($key) && !empty($key)) {

            $keys = explode('.', $key);
            $arrTmp = &$array;

            while (sizeof($keys) >= 1) {
                $k = array_shift($keys);

                if (!is_array($arrTmp)) {
                    $arrTmp = [];
                }

                if (!isset($arrTmp[$k])) {
                    $arrTmp[$k] = [];
                }

                if (sizeof($keys) === 0) {
                    $arrTmp[$k] = $value;
					$this->list = $arrTmp;
                }

                $arrTmp = &$arrTmp[$k];
            }
        }
        return $this;
    }
	
}