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
| Returns trait
|---------------------------------------------------
*/
trait ReturnsTrait {

    /**
     * AVG
     * 
     * Get the average value of a given key.
     * Example
     * Collection::collect([1, 1, 2, 4])->avg();
     * Collection::collect([['price' => 10],['price' => 10],['price' => 20],['price' => 40]])->avg("price");
     */
    public function avg($key='')
    {
        if(!empty($key)) {
            $count = sizeof($this->list);
            $average = array_sum(array_column($this->list, $key)) / $count;
        }else{
            $arr = array_filter($this->list);
            $average = array_sum($arr)/count($arr);
        }
        
        return $average;
    }

    /**
     * All
     * 
     * Get all of the items in the collection.
     * Example
     * Collection::collect([1, 1, 2, 4])->all();
     */
    public function all()
    {
        return $this->list;
    }

    /**
     * Contains
     * Determine if an item (value) exists in the collection.
     * 
     * Example
     *  Collection::collect([1, 2, 3, 4, 5])->contains(1);
     *  Collection::collect(['Name'=>"Hassan", 'Country'=>"Egypt"])->contains("Egypt");
     */
    public function contains($value)
    {
        return in_array($value, $this->list);
    }

    /**
     * Count the number of items in the collection.
     * 
     * Example
     *  Collection::collect([1, 2, 3, 4, 5])->count();
     *  Collection::collect(['Name'=>"Hassan", 'Country'=>"Egypt"])->count();
     */
    public function count()
    {
        return count($this->list);
    }

    /**
     * Count the number of items in the collection by a field.
     * 
     * Example
     * Collection::collect([1, 2, 3, 3])->countBy(3);
     */
    public function countBy($field=null)
    {
        if(!is_null($field)){
            return array_count_values($this->list)[$field] ?? false;
        }

        return array_count_values($this->list);
    }
    
    /**
     * Determine if all items pass the given truth test.
     *
     * Example
     * Collection::collect([1, 2, 3, 4])->every(function ($value, $key) { return $value > 2; });
     */
    public function every($key, $operator = null, $value = null)
    {
        if (func_num_args() === 1) {
            $callback = $key;

            foreach ($this as $k => $v) {
                if (! $callback($v, $k)) {
                    return false;
                }
            }

            return true;
        }

        return $this->every($this->operatorForWhere(...func_get_args()));
    }
 
    /**
     * Convert the array into a query string.
     * 
     * Example
     * Collection::collect(['id'=>1, "page"=>2])->query();
    */
    public function query()
    {
        return http_build_query($this->list, '', '&', PHP_QUERY_RFC3986);
    }

    /**
     * Determine if an item exists in the collection by key.
     *
     * Example
     * Collection::collect(["name"=>"hassan", "country"=>"egypt"])->has("name", "country");
     * Collection::collect(["name"=>"hassan", "country"=>"egypt"])->has(["name", "age"]);
     * Collection::collect(["name"=>"hassan", "country"=>"egypt"])->has("country");
     */
    public function has($keys)
    {
        $keys = is_array($keys) ? $keys : func_get_args();

        $res = true;

        foreach($keys as $key){
            if($res === false){
                break;
            }
            if (!isset($this->list[$key])) {
                $res = false;
            }
        }

        return $res;
    }

    /**
     * Concatenate values of a given key as a string.
     *
     * Example
     * Collection::collect([1,2,3])->implode();
     * Collection::collect([1,2,3])->implode("-");
     * Collection::collect(["name"=>"hassan", "country"=>"egypt"])->implode("-");
     */
    public function implode($value='')
    {
        return implode($value, $this->list);
    }

    /**
     * Determine if the collection is empty or not.
     *
     * Example
     * Collection::collect([])->isEmpty();
     */
    public function isEmpty()
    {
        return empty($this->list);
    }

    /**
     * Determine if the collection is not empty.
     *
     * Example
     * Collection::collect([])->isNotEmpty();
     */
    public function isNotEmpty()
    {
        return ! $this->isEmpty();
    }

    /**
     * Get the max value of a given key.
     *
     * Example
     * Collection::collect([1, 2, 3, 4, 5])->max();
     * Collection::collect([ ['foo' => 10], ['foo' => 20] ])->max();
     * Collection::collect([ ['foo' => 10], ['foo' => 20], ['boo' => 30], ['boo' => 40] ])->max("boo");
     */
    public function max($column=null){

        if(!is_null($column)){
            $res = array_column($this->list, $column);
        }else {
            $res = $this->list;
        }

        $res = max(array_values($res));
        $res = (is_array($res)) ? array_values($res)[0] : $res;

        return $res;
    }

    /**
     * Get the min value of a given key.
     *
     * Example
     * Collection::collect([1, 2, 3, 4, 5])->min();
     * Collection::collect([ ['foo' => 10], ['foo' => 20] ])->min();
     * Collection::collect([ ['foo' => 10], ['foo' => 20], ['boo' => 30], ['boo' => 40] ])->min("boo");
     */
    public function min($column=null){

        if(!is_null($column)){
            $res = array_column($this->list, $column);
        }else {
            $res = $this->list;
        }

        $res = min(array_values($res));
        $res = (is_array($res)) ? array_values($res)[0] : $res;

        return $res;
    }
    
    /**
     * Pass the collection to the given callback and return the result.
     *
     * Example
     * Collection::collect([1, 2, 3])->pipe(function ($map) { return $Collection::all(); });
     */
    public function pipe(callable $callback)
    {
        return $callback($this);
    }

    /**
     * Get and remove the last item from the collection.
     *
     * Example
     * Collection::collect([1, 2, 3, 4, 5])->pop();
     */
    public function pop()
    {
        return array_pop($this->list);
    }

    /**
     * Search the collection for a given value and return the corresponding key if successful.
     *
     * Example
     * Collection::collect([2, 4, 6, 8])->search(8);
     * Collection::collect([2, 4, 6, 8])->search(4, $strict = true);
     * Collection::collect([2, 4, 6, 8])->search(function ($item, $key) { return $item > 5; });
     * 
     */
    public function search($value,  $strict = false)
    {
        if (!is_callable($value)) {
            return array_search($value, $this->list, $strict);
        }

        foreach ($this->list as $key => $item) {
            if ($value($item, $key)) {
                return $key;
            }
        }

        return false;
    }
    
    /**
     * Get the sum of the given values.
     *
     * Example
     * Collection::collect([1, 2, 3, 4, 5])->sum();
     * Collection::collect([ ['name' => 'tv', 'price' => 1], ['name' => 'phone', 'price' => 2], ])->sum();
     */
    public function sum($column='')
    {
        if(empty($column)){
            return array_sum($this->list);
        }else {
            $sumDetail = $column;
            $total = array_reduce($this->list,
                       function($runningTotal, $record) use($sumDetail) {
                           $runningTotal += $record[$sumDetail];
                           return $runningTotal;
                       },
                       0
            );
            return $total;
        }
    }

    /**
     * Returns the values from a single column.
     *
     * Example
     * Collection::collect([ ['name' => 'tv', 'price' => 1], ['name' => 'phone', 'price' => 2], ])->column('name');
     */
    public function column(string $column)
    {
        return array_column($this->list, $column);
    }

    /*
    * Returns a new object created from the resource.
    *
    */
    public function resource(callable $function)
    {
        // Data
        $data = array();

        // Map
        foreach($this->list as $array){
            $item = json_decode(json_encode($array), false);
            $result = call_user_func_array($function, [$item]);
            $data[] = $result;
        }

        // Return
        return json_decode(json_encode($data), false);
    }
}
