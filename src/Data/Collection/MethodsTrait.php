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
| Uses
|---------------------------------------------------
*/
use Kiaan\Data\Collection\WhenProxy;

/*
|---------------------------------------------------
| Methods trait
|---------------------------------------------------
*/
trait MethodsTrait {

    /**
     * Chunk the underlying collection array.
     *
     * Example
     * Collection::collect([1, 2, 3, 4, 5, 6, 7])->chunk(4);
     * Collection::collect(["Name"=>"Hassan", "Country"=>"Egypt"])->chunk(1);
     */
    public function chunk($size)
    {
        $chunks = [];

        foreach (array_chunk($this->list, $size, false) as $chunk) {
            $chunks[] = $chunk;
        }

        return $this->collect($chunks);
    }
    
    /**
     * Collapse the collection of items into a single array.
     *
     * Example
     * Collection::collect([[1, 2, 3],[4, 5, 6],[7, 8, 9]])->collapse();
     * Collection::collect([["Name"=>"Hassan", "Country"=>"Egypt"],[1, 2, 3]])->collapse();
     */
    public function collapse()
    {
        $array = $this->list;
        $results = [];

        foreach ($array as $values) {
            $values = $values;
            $results[] = $values;
        }

        $collapse = array_merge([], ...$results);

        return $this->collect($collapse);
    }

    /**
    * Combine
    * Create a collection by using this collection for keys and another for its values.
    *
    * Example
    * Collection::collect(['name', 'job'])->combine(['Hassan', 'programmer']);
    */
    public function combine($values)
    {
        return $this->collect(array_combine($this->list, $values));
    }

    /**
     * Cross join with the given lists, returning all possible permutations.
     *
     * Example
     * Collection::collect([1, 2])->crossJoin(['a', 'b']);
     */
    public function crossJoin(...$lists)
    {
        return $this->collect($this->arrCrossJoin(
            $this->list,
            ...array_map([$this, 'getArrayableItems'], $lists)
        ));
    }

    /**
     * Get the items in the collection that are not present in the given items.
     *
     * Example
     * Collection::collect([1, 2, 3, 4, 5])->diff([2, 4, 6, 8]);
     * Collection::collect(['color' => 'orange', 'type' => 'fruit', 'remain' => 6])->diff(['color' => 'yellow', 'type' => 'fruit', 'remain' => 3, 'used' => 6]);
     */
    public function diff($items)
    {
        if (preg_match('/[a-z]/i', implode(array_keys($items)))) {
            return $this->collect(array_diff_assoc($this->list, $items));
        } else {
            return $this->collect(array_diff($this->list, $items));
        }
    }

    /**
     * Get the items in the collection whose keys are not present in the given items.
     *
     * Example
     * Collection::collect(['one' => 10, 'two' => 20, 'three' => 30, 'four' => 40, 'five' => 50])->diffKeys(['two' => 2, 'four' => 4, 'six' => 6, 'eight' => 8]);
     */
    public function diffKeys($items)
    {
        return $this->collect(array_diff_key($this->list, $items));
    }

    /**
     * Get the items in the collection whose keys are not present in the given items.
     *
     * Example
     * Collection::collect(['a', 'b', 'a', 'c', 'b'])->duplicates();
     * Collection::collect([ ['email' => 'mohammed@example.com', 'position' => 'Developer'], ['email' => 'hassan@example.com', 'position' => 'Designer'], ['email' => 'ali@example.com', 'position' => 'Developer'], ])->duplicates('position');
     */
    public function duplicates($column=null)
    {
        if (is_null($column)) {
            $arr = $this->list;
            $duplicates = array_diff_key($arr, array_unique($arr));
        } else {
            $arr = array_column($this->list, $column);
            $duplicates = array_diff_key($arr, array_unique($arr));
        }

        return $this->collect($duplicates);
    }

    /**
     * Execute a callback over each item.
     *
     * Example
     * Collection::collect([1, 2])->each(function ($item, $key) { // code });
     */
    public function each(callable $callback)
    {
        foreach ($this->list as $key => $item) {
            if ($callback($item, $key) === false) {
                break;
            }
        }

        return clone($this);
    }

    /**
     * Create a new collection consisting of every n-th element.
     *
     * Example
     * Collection::collect(['a', 'b', 'c', 'd', 'e', 'f'])->nth(4);
     * Collection::collect(['a', 'b', 'c', 'd', 'e', 'f'])->nth(4,1);
     */
    public function nth($step, $offset = 0)
    {
        $new = [];

        $position = 0;

        foreach ($this->list as $item) {
            if ($position % $step === $offset) {
                $new[] = $item;
            }

            $position++;
        }

        return $this->collect($new);
    }
    
    /**
     * Get all items except for those with the specified keys.
     *
     * Example
     * Collection::collect(['ID' => 1, 'Name' => "Hassan", 'Country' => "Egypt"])->except(['ID', 'Country']);
     * Collection::collect(['ID' => 1, 'Name' => "Hassan", 'Country' => "Egypt"])->except('ID', 'Country');
    */
    public function except($keys)
    {
        $keys = is_array($keys) ? $keys : func_get_args();

        return $this->collect(array_diff_key($this->list, array_flip($keys)));
    }

    /**
     * Run a filter over each of the items.
     *
     * Example
     * Collection::collect([1, 2, 3, 4])->filter(function ($value) { return $value < 3; });
     * Collection::collect([1, 2, 3, null, false, '', 0, []])->filter();
     *
     */
    public function filter(callable $callback = null)
    {
        if ($callback) {
            return $this->collect(array_filter($this->list, $callback));
        }

        return $this->collect(array_filter($this->list));
    }

    /**
     * Get the first item from the collection.
     *
     * Example
     * Collection::collect([1, 2, 3, 4])->first();
     * Collection::collect([1, 2, 3, 4])->first(function ($value, $key) { return $value > 2; });
     */
    public function first(callable $callback = null, $default = null)
    {
        if (is_null($callback)) {
            return count($this->list) > 0 ? reset($this->list) : null;
        }

        return $this->arrFirst($this->list, $callback, $default);
    }
    
    /**
     * Get a flattened array of the items in the collection.
     *
     * Example
     * Collection::collect(['name'=>'Hassan','languages'=>['php', 'javascript']])->flatten();
     * Collection::collect([1,2,3])->flatten();
     */
    public function flatten()
    {
        $array = $this->list;
        $return = array();

        while (count($array)) {
            $value = array_shift($array);
            if (is_array($value)) {
                foreach ($value as $sub) {
                    $array[] = $sub;
                }
            } else {
                $return[] = $value;
            }
        }

        return $this->collect($return);
    }

    /**
     * Flip the items in the collection.
     *
     * Example
     * Collection::collect(['Name' => 'Hassan', 'Framework' => 'Kiaan'])->flip();
     * Collection::collect(['a','b','c'])->flip();
    */
    public function flip()
    {
        return $this->collect(array_flip($this->list));
    }

    /**
     * Remove an item from the collection by key.
     *
     * Example
     * Collection::collect(['a','b','c'])->forget(0);
     * Collection::collect(['ID' => 1, 'Name' => "Hassan", 'Country' => "Egypt"])->forget(['ID', 'Country']);
     */
    public function forget($keys)
    {
        $keys = is_array($keys) ? $keys : func_get_args();
        
        foreach ((array)$keys as $key) {
            unset($this->list[$key]);
        }

        return clone($this);
    }

    /**
     * "Paginate" the collection by slicing it into a smaller collection.
     * 
     * Example
     * Collection::collect([1, 2, 3, 4, 5, 6, 7, 8, 9])->paginate(2, 3);
     */
    public function paginate($page, $perPage)
    {
        $offset = max(0, ($page - 1) * $perPage);

        return $this->collect(array_slice($this->list, $offset, $perPage, true));
    }
    
    /**
     * Function that groups an array of associative arrays by some key.
     *
     * Example
     * Collection::collect([ ['id' => '10', 'product' => 'pc'], ['id' => '10', 'product' => 'mobile'], ['id' => '11', 'product' => 'laptop'], ])->groupBy('id');
     */
    function groupBy($key) {
        $result = array();

        foreach($this->list as $val) {
            if(array_key_exists($key, $val)){
                $result[$val[$key]][] = $val;
            }else{
                $result[""][] = $val;
            }
        }

        return $this->collect($result);
    }
    
    /**
     * Intersect the collection with the given items.
     *
     * Example
     * Collection::collect(['mobile', 'pc', 'laptop'])->intersect(['tv', 'pc', 'laptop']);
     */
    public function intersect($items)
    {
        return $this->collect(array_intersect($this->list, $items));
    }

    /**
     * Intersect the collection with the given items by key.
     *
     * Example
     * Collection::collect(['serial' => 'HS321', 'type' => 'tv', 'year' => 2020])->intersectByKeys(['reference' => 'HS123', 'type' => 'mobile', 'year' => 2021]);
     */
    public function intersectByKeys($items)
    {
        return $this->collect(array_intersect_key($this->list, $items));
    }

    /**
     * Get the keys of the collection items.
     *
     * Example
     * Collection::collect(["name"=>"hassan", "country"=>"egypt"])->keys();
     */
    public function keys()
    {
        return $this->collect(array_keys($this->list));
    }

    /**
     * Get the first item from the collection.
     *
     * Example
     * Collection::collect([1, 2, 3, 4])->last();
     * Collection::collect([1, 2, 3, 4])->last(function ($value, $key) { return $value < 3; });
     */
    public function last(callable $callback = null, $default = null)
    {
        return $this->arrLast($this->list, $callback, $default);
    }

    /**
     * Run a map over each of the items.
     * 
     * Example
     * Collection::collect([1, 2, 3, 4])->map(function ($item, $key) { return $item * 2; });
     */
    public function map(callable $callback)
    {
        $keys = array_keys($this->list);

        $items = array_map($callback, $this->list, $keys);

        return $this->collect(array_combine($keys, $items));
    }

    /**
     * Run an associative map over each of the items.
     *
     * Example
     * Collection::collect([ [ 'name' => 'hassan', 'department' => 'Sales', 'email' => 'hassan@example.com', ], [ 'name' => 'ahmed', 'department' => 'Marketing', 'email' => 'ahmed@example.com', ] ])->mapWithKeys(function ($item) { return [$item['email'] => $item['name']]; });
     */
    public function mapWithKeys(callable $callback)
    {
        $result = [];

        foreach ($this->list as $key => $value) {
            $assoc = $callback($value, $key);

            foreach ($assoc as $mapKey => $mapValue) {
                $result[$mapKey] = $mapValue;
            }
        }

        return $this->collect($result);
    }

    /**
     * Run an associative map over each of the items.
     *
     * Example
     * Collection::collect(['product_id' => 1, 'price' => 100])->merge(['price' => 200, 'discount' => false]);
     * Collection::collect(['Desk', 'Chair'])->merge(['Bookcase', 'Door']);
     */
    public function merge($items)
    {
        return $this->collect(array_merge($this->list, $items));
    }

    /**
     * Recursively merge the collection with the given items.
     *
     * Example
     * Collection::collect(['product_id' => 1, 'price' => 100])->mergeRecursive([ 'product_id' => 2, 'price' => 200, 'discount' => false ]);
     */
    public function mergeRecursive($items)
    {
        return $this->collect(array_merge_recursive($this->list, $items));
    }

    /**
     * Get the items with the specified keys.
     *
     * Example
     * Collection::collect([ 'product_id' => 1, 'name' => 'Desk', 'price' => 100, 'discount' => false ])->only(['product_id', 'name']);
     */
    public function only($keys)
    {
        if (is_null($keys)) {
            return $this->collect($this->list);
        }

        $keys = is_array($keys) ? $keys : func_get_args();

        $res = array_intersect_key($this->list, array_flip((array) $keys));

        return $this->collect($res);
    }

    /**
     * Pad collection to the specified length with a value.
     *
     * Example
     * Collection::collect(['A', 'B', 'C'])->pad(5, 0);
     * Collection::collect(['A', 'B', 'C'])->pad(-5, 0);
     */
    public function pad($size, $value)
    {
        return $this->collect(array_pad($this->list, $size, $value));
    }

    /**
     * Push an item onto the beginning of the collection.
     *
     * Example
     * Collection::collect([1, 2, 3, 4, 5])->prepend(0);
     * Collection::collect([1, 2, 3, 4, 5])->prepend(0, 'zero');
     */
    public function prepend($value, $key = null)
    {
        $this->list = $this->arrPrepend($this->list, ...func_get_args());

        return clone($this);
    }

    /**
     * Push one or more items onto the end of the collection.
     *
     * Example
     * Collection::collect([1, 2, 3, 4])->push(5);
     */
    public function push(...$values)
    {
        foreach ($values as $value) {
            $this->list[] = $value;
        }

        return clone($this);
    }

    /**
     * Put an item in the collection by key.
     *
     * Example
     * Collection::collect([1, 2, 3, 4])->put(3, 5);
     */
    public function put($key, $value)
    {
        $this->offsetSet($key, $value);

        return clone($this);
    }

    /**
     * Get one or a specified number of items randomly from the collection.
     *
     * Example
     * Collection::collect([1, 2, 3, 4, 5])->random();
     * Collection::collect([1, 2, 3, 4, 5])->random(3);
     */
    public function random($number = null)
    {
        if (is_null($number)) {
            return $this->arrRandom($this->list);
        }

        return $this->collect($this->arrRandom($this->list, $number));
    }

    /**
     * Reduce the collection to a single value.
     *
     * Example
     * Collection::collect([1, 2, 3])->reduce(function ($carry, $item) { return $carry + $item; });
     * Collection::collect([1, 2, 3])->reduce(function ($carry, $item) { return $carry + $item; }, 4);
     */
    public function reduce(callable $callback, $initial = null)
    {
        return array_reduce($this->list, $callback, $initial);
    }

    /**
     * Create a collection of all elements that do not pass a given truth test.
     *
     * Example
     * Collection::collect([1, 2, 3, 4])->reject(function ($value) { return $value > 2; });
     */
    public function reject(callable $callback)
    {
        if (! is_string($callback) && is_callable($callback)) {
            return $this->filter(function ($item) use ($callback) {
                return !$callback($item);
            });
        }

        return $this->filter(function ($item) use ($callback) {
            return $item != $callback;
        });
    }

    /**
     * Replace the collection items with the given items.
     *
     * Example
     * Collection::collect(['Hassan', 'Mohammed', 'Ahmed'])->replace([1 => 'Ali', 3 => 'Taha']);
     */
    public function replace($items)
    {
        return $this->collect(array_replace($this->list, $items));
    }

    /**
     * Recursively replace the collection items with the given items.
     *
     * Example
     * Collection::collect(['Hassan','Ahmed',['Ali','Mohhamed','Ibrahim']])->replaceRecursive([ 'Salah', 2 => [1 => 'Yasmin'] ]);
     */
    public function replaceRecursive($items)
    {
        return $this->collect(array_replace_recursive($this->list, $items));
    }

    /**
     * Reverse items order.
     *
     * Example
     * Collection::collect(['a', 'b', 'c', 'd', 'e'])->reverse();
     */
    public function reverse()
    {
        return $this->collect(array_reverse($this->list, true));
    }
    
    /**
     * Get and remove the first item from the collection.
     *
     * Example
     * Collection::collect(['a', 'b', 'c', 'd', 'e'])->shift();
     */
    public function shift()
    {
        array_shift($this->list);
        return clone($this);
    }

    /**
     * Shuffle the items in the collection.
     *
     * Example
     * Collection::collect([1, 2, 3, 4, 5])->shuffle();
     */
    public function shuffle($seed = null)
    {
        if (is_null($seed)) {
            shuffle($this->list);
        } else {
            mt_srand($seed);
            shuffle($this->list);
            mt_srand();
        }

        return clone($this);
    }

    /**
     * Skip the first {$count} items.
     *
     * Example
     * Collection::collect([1, 2, 3, 4, 5, 6, 7, 8, 9, 10])->skip(5);
     */
    public function skip($count)
    {
        return $this->collect(array_slice($this->list, $count, null, true));
    }

    /**
     * Slice the underlying collection array.
     *
     * Example
     * Collection::collect([1, 2, 3, 4, 5, 6, 7, 8, 9, 10])->slice(4);
     * Collection::collect([1, 2, 3, 4, 5, 6, 7, 8, 9, 10])->slice(4, 2);
     */
    public function slice($offset, $length = null)
    {
        return $this->collect(array_slice($this->list, $offset, $length, true));
    }

    /**
     * Sort through each item with a callback.
     *
     * Example
     * Collection::collect([5, 3, 1, 2, 4])->sort();
     */
    public function sort($callback = null)
    {
        $items = $this->list;

        $callback && is_callable($callback)
        ? uasort($items, $callback)
        : asort($items);

        return $this->collect($items);
    }

    /**
     * Sort items in descending order.
     *
     * Example
     * Collection::collect([5, 3, 1, 2, 4])->sortDesc();
     */
    public function sortDesc()
    {
        $items = $this->list;

        arsort($items, SORT_REGULAR);

        return $this->collect($items);
    }

    /**
     * Sort the collection keys.
     *
     * Example
     * Collection::collect([ 'id' => 123, 'first' => 'Hassan', 'last' => 'Kerdash'])->sortKeys();
     */
    public function sortKeys($options = SORT_REGULAR, $descending = false)
    {
        $items = $this->list;

        $descending ? krsort($items, $options) : ksort($items, $options);

        return $this->collect($items);
    }

    /**
     * Sort the collection keys in descending order.
     *
     * Example
     * Collection::collect([ 'id' => 123, 'first' => 'Hassan', 'last' => 'Kerdash'])->sortKeysDesc();
     */
    public function sortKeysDesc()
    {
        return $this->sortKeys(SORT_REGULAR, true);
    }

    /**
     * Splice a portion of the underlying collection array.
     *
     * Example
     * Collection::collect([1, 2, 3, 4, 5])->splice(2);
     * Collection::collect([1, 2, 3, 4, 5])->splice(2, 1);
     * Collection::collect([1, 2, 3, 4, 5])->splice(2, 1, [10, 11]);
     */
    public function splice($offset, $length = null, $replacement = [])
    {
        if (func_num_args() === 1) {
            return $this->collect(array_splice($this->list, $offset));
        }

        return $this->collect(array_splice($this->list, $offset, $length, $replacement));
    }

    /**
     * Split a collection into a certain number of groups.
     *
     * Example
     * Collection::collect([1, 2, 3, 4, 5])->split(3);
     */
    public function split($numberOfGroups)
    {
        if ($this->isEmpty()) {
            return $this->collect;
        }

        $groups = $this->collect;

        $groupSize = floor($this->count() / $numberOfGroups);

        $remain = $this->count() % $numberOfGroups;

        $start = 0;

        for ($i = 0; $i < $numberOfGroups; $i++) {
            $size = $groupSize;

            if ($i < $remain) {
                $size++;
            }

            if ($size) {
                $groups->push($this->collect(array_slice($this->list, $start, $size)));

                $start += $size;
            }
        }

        return $groups;
    }

    /**
     * Split a collection into a certain number of groups, and fill the first groups completely.
     *
     * Example
     * Collection::collect([1, 2, 3, 4, 5, 6, 7, 8, 9, 10])->splitIn(3);
     */
    public function splitIn($numberOfGroups)
    {
        return $this->chunk(ceil($this->count() / $numberOfGroups));
    }

    /**
     * Take the first or last {$limit} items.
     *
     * Example
     * Collection::collect([0, 1, 2, 3, 4, 5])->take(3);
     * Collection::collect([0, 1, 2, 3, 4, 5])->take(-2);
     */
    public function take($limit)
    {
        if ($limit < 0) {
            return $this->slice($limit, abs($limit));
        }

        return $this->slice(0, $limit);
    }

    /**
     * Transform each item in the collection using a callback.
     *
     * Example
     * Collection::collect([1, 2, 3, 4, 5])->transform(function ($item, $key) { return $item * 2; });
     */
    public function transform(callable $callback)
    {
        $this->list = $this->map($callback)->all();

        return clone($this);
    }

    /**
     * Union the collection with the given items.
     *
     * Example
     * Collection::collect([1 => ['a'], 2 => ['b']])->union([3 => ['c'], 1 => ['b']]);
     */
    public function union($items)
    {
        return $this->collect($this->list + $items);
    }

    /**
     * Return only unique items from the collection array.
     *
     * Example
     * Collection::collect(["name"] => ["Hassan", "Ali", "Hassan", "Ahmed"])->unique("name");
     * Collection::collect([ ["id"=>1, "value"=>1], ["id"=>2, "value"=>2], ["id"=>3, "value"=>1], ])->unique("value");
     */
    public function unique($column='')
    {
        if(empty($column)){
            return array_unique($this->list);
        }else{
            return array_unique(array_column($this->list, $column));
        }
    }

    /**
     * Filter items by the given key value pair.
     * 
     * Example
     * Collection::collect([ ['product' => 'Pc', 'price' => 200], ['product' => 'Phone', 'price' => 100], ['product' => 'Mobile', 'price' => 150], ['product' => 'Laptop', 'price' => 100], ])->where('price', 100);
     * Collection::collect([ ['product' => 'Pc', 'price' => 200], ['product' => 'Phone', 'price' => 100], ['product' => 'Mobile', 'price' => 150], ['product' => 'Laptop', 'price' => 100], ])->where('price', '=' ,100);
     */
    public function where($key, $operator = null, $value = null)
    {
        return $this->filter($this->operatorForWhere(...func_get_args()));
    }

    /**
     * Filter items by the given key value pair using strict comparison.
     *
     * Example
     * Collection::collect([ ['product' => 'Pc', 'price' => 200], ['product' => 'Phone', 'price' => 100], ['product' => 'Mobile', 'price' => 150], ['product' => 'Laptop', 'price' => 100], ])->whereStrict('price', 100);
     */
    public function whereStrict($key, $value)
    {
        return $this->where($key, '===', $value);
    }

    /**
     * Filter items such that the value of the given key is between the given values.
     *
     * Example
     * Collection::collect([ ['product' => 'TV', 'price' => 200], ['product' => 'Laptop', 'price' => 80], ['product' => 'Speaker', 'price' => 150], ['product' => 'Mobile', 'price' => 30], ['product' => 'PC', 'price' => 100], ])->whereBetween('price', [100, 200]);
     */
    public function whereBetween($key, $values)
    {
        return $this->where($key, '>=', reset($values))->where($key, '<=', end($values));
    }

    /**
     * Filter items by the given key value pair.
     *
     * Example
     * Collection::collect([ ['product' => 'PC', 'price' => 200], ['product' => 'Mobile', 'price' => 100], ['product' => 'Phone', 'price' => 150], ['product' => 'TV', 'price' => 100], ])->whereIn('price', [150, 200]);
     */
    public function whereIn($key, $values, $strict = false)
    {
        $values = $this->getArrayableItems($values);

        return $this->filter(function ($item) use ($key, $values, $strict) {
            return in_array($this->arr_data_get($item, $key), $values, $strict);
        });
    }

    /**
     * Filter items by the given key value pair using strict comparison.
     *
     * Example
     * Collection::collect([ ['product' => 'PC', 'price' => 200], ['product' => 'Mobile', 'price' => 100], ['product' => 'Phone', 'price' => 150], ['product' => 'TV', 'price' => 100], ])->whereInStrict('price', [150, 200]);
     */
    public function whereInStrict($key, $values)
    {
        return $this->whereIn($key, $values, true);
    }
    
    /**
     * Filter the items, removing any items that don't match the given type.
     *
     * Example
     * Collection::collect([ new User, new User, new Admin, ])->whereInstanceOf(User::class);
     */
    public function whereInstanceOf($type)
    {
        return $this->filter(function ($value) use ($type) {
            return $value instanceof $type;
        });
    }

    /**
     * Filter items such that the value of the given key is not between the given values.
     *
     * Example
     * Collection::collect([ ['product' => 'TV', 'price' => 200], ['product' => 'Laptop', 'price' => 80], ['product' => 'Speaker', 'price' => 150], ['product' => 'Mobile', 'price' => 30], ['product' => 'PC', 'price' => 100], ])->whereNotBetween('price', [100, 200]);
     */
    public function whereNotBetween($key, $values)
    {
        return $this->filter(function ($item) use ($key, $values) {
            return $this->arr_data_get($item, $key) < reset($values) || $this->arr_data_get($item, $key) > end($values);
        });
    }

    /**
     * Filter items by the given key value pair.
     *
     * Example
     * Collection::collect([ ['product' => 'PC', 'price' => 200], ['product' => 'Mobile', 'price' => 100], ['product' => 'Phone', 'price' => 150], ['product' => 'TV', 'price' => 100], ])->whereNotIn('price', [150, 200]);
     */
    public function whereNotIn($key, $values, $strict = false)
    {
        return $this->reject(function ($item) use ($key, $values, $strict) {
            return in_array($this->arr_data_get($item, $key), $values, $strict);
        });
    }

    /**
     * Filter items by the given key value pair using strict comparison.
     *
     * Example
     * Collection::collect([ ['product' => 'PC', 'price' => 200], ['product' => 'Mobile', 'price' => 100], ['product' => 'Phone', 'price' => 150], ['product' => 'TV', 'price' => 100], ])->whereNotInStrict('price', [150, 200]);
     */
    public function whereNotInStrict($key, $values)
    {
        return $this->whereNotIn($key, $values, true);
    }
    
    /**
     * Filter items where the value for the given key is not null.
     *
     * Example
     * Collection::collect([ ['name' => 'pc'], ['name' => null], ['name' => 'phone'], ])->whereNotNull('name');
     */
    public function whereNotNull($key = null)
    {
        return $this->where($key, '!==', null);
    }

    /**
     * Filter items where the value for the given key is null.
     *
     * Example
     * Collection::collect([ ['name' => 'pc'], ['name' => null], ['name' => 'phone'], ])->whereNull('name');
     */
    public function whereNull($key = null)
    {
        return $this->whereStrict($key, null);
    }

    /**
     * Zip the collection together with one or more arrays.
     *
     * Example
     * Collection::collect(['Phone', 'PC'])->zip([100, 200]);
     */
    public function zip($items)
    {
        $arrayableItems = array_map(function ($items) {
            return $items;
        }, func_get_args());

        $params = array_merge([function () {
            return $this->collect(func_get_args());
        }, $this->list], $arrayableItems);

        return $this->collect(array_map(...$params));
    }
    
    /**
     * Apply the callback if the value is truthy.
     *
     * Example
     * Collection::collect([1,2,3])->when(true, function ($map) {return $map->push(4);});
     */
    public function when($value, callable $callback = null, callable $default = null)
    {
        if (! $callback) {
            return new WhenProxy($this, $value);
        }

        if ($value) {
            return $callback($this, $value);
        } elseif ($default) {
            return $default($this, $value);
        }

        return clone($this);
    }

    /**
     * Apply the callback if the collection is empty.
     *
     * Example
     * Collection::collect([])->whenEmpty(function ($map) { return $map->push('Hassan'); });
     */
    public function whenEmpty(callable $callback, callable $default = null)
    {
        return $this->when(empty($this->list), $callback, $default);
    }

    /**
     * Apply the callback if the collection is not empty.
     *
     * Example
     * Collection::collect(['Ahmed', 'Mohammed'])->whenNotEmpty(function ($map) { return $map->push('Hassan'); });
     */
    public function whenNotEmpty(callable $callback, callable $default = null)
    {
        return $this->when(!empty($this->list), $callback, $default);
    }

    /**
     * Create a collection with the given range.
     *
     * Example
     * Collection::range(1, 10);
     */
    public function range($from, $to)
    {
        return $this->collect(range($from, $to));
    }

}