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
| Excute trait
|---------------------------------------------------
*/
trait ExcuteTrait {

    /**
     * @param bool|string $type
     * @param string|null $argument
     *
     * @return mixed|string
     */
    public function first($type = null, $argument = null)
    {
        $this->limit = 1;
        $query = $this->get(true);

        if ($type === true) {
            return $query;
        }

        $query = $this->query($query, false, $type, $argument);

        // Count
        if(is_array($this->result)){
            $this->count = count($this->result);
        }else{
            $this->count = count(array($this->result));
        }

        // Excute getter method
        $this->excuteGetter();

        // Return
        $this->data = $this->result;

        return clone($this);
    }

    /**
     */
    public function firstOrFail()
    {
        // Result
        $result = $this->first();

        // Fail
        if(!$result->data){
            throw new \Exception("Not found any rows.");
        }

        // Return data
        return $result;
    }

    /**
     * @param bool|string $type
     * @param string|null $argument
     *
     * @return mixed|string
     */
    public function get($type = null, $argument = null)
    {
        $query = 'SELECT ' . $this->distinct . $this->select . ' FROM ' . $this->table;

        if (! is_null($this->join)) {
            $query .= $this->join;
        }

        if (! is_null($this->where)) {
            $query .= ' WHERE ' . $this->where;
        }

        if (! is_null($this->groupBy)) {
            $query .= ' GROUP BY ' . $this->groupBy;
        }

        if (! is_null($this->having)) {
            $query .= ' HAVING ' . $this->having;
        }

        if (! is_null($this->orderBy)) {
            $query .= ' ORDER BY ' . $this->orderBy;
        }

        if (! is_null($this->limit)) {
            $query .= ' LIMIT ' . $this->limit;
        }

        if (! is_null($this->offset)) {
            $query .= ' OFFSET ' . $this->offset;
        }

        if ($type === true) {
            return $query;
        }

        $query = $this->query($query, true, $type, $argument);

        // Size
        $this->size = sizeof($this->result);

        // Count
        $this->count = count($this->result);

        // Excute getter method
        $this->excuteGetter();
        
        // Return        
        $this->data = $this->result;
        
        return clone($this);
    }

    /**
     * 
     * Excute getter method
     * 
     */
    protected function excuteGetter()
    {
        if(!empty($this->only)){
            $this->only($this->only);
        }

        if(!empty($this->hidden)){
            $this->hidden($this->hidden);
        }

        $func = $this->__getter();
        if($func!==false && is_callable($func)){
            $this->map($func);
        }
    }

    /**
     * 
     * Excute setter method
     * 
     */
    protected function excuteSetter($data)
    {
        $items = json_decode(json_encode($data), false);

        $items = $this->__setter($items);

        $items = (empty($items)) ? $data : $items;

        return json_decode(json_encode($items), true);
    }

    /**
     * @param bool|string $type
     * @param string|null $argument
     *
     * @return mixed|string
     */
    public function all($type = null, $argument = null)
    {
        return $this->get($type, $argument);
    }

   /**
     * @param $perPage
     *
     * @return self::class
     */
    public function page($perPage, $page=null)
    {
         // page
        if($page < 1){ $page = 1; }

        // total
        $result = $this->get();

        $total = $this->size();
        
        //Pagination
        $this->pagination($perPage, $page);

        $this->query = $this->query . " LIMIT {$this->limit} OFFSET {$this->offset}";

        // Result
        $this->result = $this->fetch(null, null, true);

        // Pages
        $pages = ceil($total / $perPage);

        // Next & back
        if($page > $pages){$page = $pages;}
        $next = $page+1;
        $back = $page-1;
        if($page >= $pages){$next = 0;}

        // Page
        $page = (object) [
            "pages" => $pages,
            "current" => $page,
            "total" => $total,
            "count" => $perPage,
            "next" => $next,
            "back" => $back
        ];

        $this->page = $page;

        // Count
        $this->count = count($this->result);

        // Return
        $this->data = $this->result;

        return clone($this);
    }

    /**
     * @param string      $field
     *
     * @return $this
     */
    public function max($field)
    {
        $column = 'MAX(' . $field . ')' . (!is_null(null) ? ' AS ' . null : '');
        $this->optimizeSelect($column);
        foreach ($this->first()->data() as $result) break;

        return $result;
    }

    /**
     * @param string      $field
     *
     * @return $this
     */
    public function exists()
    {
        return ($this->get()->count()!=0) ? true : false ;
    }

    /**
     * @param string      $field
     *
     * @return $this
     */
    public function min($field)
    {
        $column = 'MIN(' . $field . ')' . (!is_null(null) ? ' AS ' . null : '');
        $this->optimizeSelect($column);
        foreach ($this->first()->data() as $result) break;

        return $result;
    }

    /**
     * @param string      $field
     *
     * @return $this
     */
    public function sum($field)
    {
        $column = 'SUM(' . $field . ')' . (!is_null(null) ? ' AS ' . null : '');
        $this->optimizeSelect($column);
        foreach ($this->first()->data() as $result) break;

        return $result;
    }

    /*
    * Count
    *  
    */
    public function count($field='id')
    {
        $column = 'COUNT(' . $field . ')' . (!is_null(null) ? ' AS ' . null : '');
        $this->optimizeSelect($column);
        foreach ($this->first()->data() as $result) break;

        return $result;
    }

    /**
     * @param string      $field
     *
     * @return $this
     */
    public function avg($field)
    {
        $column = 'AVG(' . $field . ')' . (!is_null(null) ? ' AS ' . null : '');
        $this->optimizeSelect($column);
        foreach ($this->first()->data() as $result) break;

        return $result;
    }

   /**
     * @param array $data
     * @param bool  $type
     *
     * @return bool|string|int|null
     */
    public function insert(array $data=array(), $type = false)
    {
        if(empty($data)){
            $data = json_decode(json_encode($this->data), true);
            $table = $this->cache_some_query['table'];
        }else{
            $table = $this->table;
        }
        
        // Excute setter method
        $data = $this->excuteSetter($data);

        // Query
        $query = 'INSERT INTO ' . $table;

        $values = array_values($data);
        if (isset($values[0]) && is_array($values[0])) {
            $column = implode(', ', array_keys($values[0]));
            $query .= ' (' . $column . ') VALUES ';
            foreach ($values as $value) {
                $val = implode(', ', array_map([$this, 'escape'], $value));
                $query .= '(' . $val . '), ';
            }
            $query = trim($query, ', ');
        } else {
            $column = implode(', ', array_keys($data));
            $val = implode(', ', array_map([$this, 'escape'], $data));
            $query .= ' (' . $column . ') VALUES (' . $val . ')';
        }

        if ($type === true) {
            return $query;
        }

        if ($this->query($query, false)) {
            $this->insertId = ($this->pdo->lastInsertId() + count($data)) - 1;
            
            return $this->lastId();
        }

        return false;
    }

    /*
    * Push update from model
    */
    protected function pushUpdate($not_string=false, $type=false)
    {
        // Data
        $data = json_decode(json_encode($this->data), true);

        // Update
        $query = 'UPDATE ' . $this->cache_some_query['table'] . ' SET ';
        $values = [];

        foreach ($data as $column => $val) {
            $val = ($not_string == true) ? trim($this->escape($val), "'") : $this->escape($val);
            $values[] = $column . '=' . $val;
        }
        $query .= implode(',', $values);

        if (!is_null($this->cache_some_query['where'])) {
            $query .= ' WHERE ' . $this->cache_some_query['where'];
        }

        if (!is_null($this->cache_some_query['orderBy'])) {
            $query .= ' ORDER BY ' . $this->cache_some_query['orderBy'];
        }

        if (!is_null($this->cache_some_query['limit'])) {
            $query .= ' LIMIT ' . $this->cache_some_query['limit'];
        }

        return $type === true ? $query : $this->query($query, false);
    }

    /**
     * @param array $data
     * @param bool  $type
     *
     * @return mixed|string
     */
    public function update(array $data=array(), $not_string=false, $type=false)
    {
        //Push update from model
        if(empty($data)){
            return $this->pushUpdate(false, false);
        }

        // Update
        $query = 'UPDATE ' . $this->table . ' SET ';
        $values = [];

        // Excute setter method
        $data = $this->excuteSetter($data);

        foreach ($data as $column => $val) {
            $val = ($not_string == true) ? trim($this->escape($val), "'") : $this->escape($val);
            $values[] = $column . '=' . $val;
        }
        $query .= implode(',', $values);

        if (!is_null($this->where)) {
            $query .= ' WHERE ' . $this->where;
        }

        if (!is_null($this->orderBy)) {
            $query .= ' ORDER BY ' . $this->orderBy;
        }

        if (!is_null($this->limit)) {
            $query .= ' LIMIT ' . $this->limit;
        }

        return $type === true ? $query : $this->query($query, false);
    }

    /*
    * It tries to find a model matching the attributes you pass in the first parameter.
    *  If a model is not found, it automatically creates and saves a new Model after applying any attributes passed in the second parameter
    *
    */
    public function firstOrInsert(array $data){
        // Raw
        $raw = clone($this);

        // Result
        $result = $this->first();
        
        // Insert
        if($this->count==0){
            return $raw->insert($data);
        }

        // Return
        return $result;
    }
    
    /*
    * The method retrieves the first Model from a query, or if no matching Model is found, it will call a callback passed.
    *
    */
    public function firstOr(callable $callback){
        $result = $this->first();
        
        if($this->count==0){
            return call_user_func($callback);
        }

        return $result;
    }

    /*
    * Update an existing record in the database if matching the condition or create if no matching record exists.
    *
    */
    public function updateOrInsert(array $conditions, array $data){
        // Conditions
        foreach ($conditions as $key => $condition) {
            $this->where($key, $condition);
        }

        // Raw
        $raw = clone($this);

        // Result
        $result = $this->get();

        // Count
        $count = count($result->data());

        // Insert
        if($count == 0){
           return $this->insert($data);
        }

        // Update
        return $raw->update($data);
    }

    /*
    * Incrementing.
    *
    */
    public function increment($column, $value=1){
        $data = array($column => $column.'+'.$value);

        return $this->update($data, true, false);
    }

    /*
    * Decrementing .
    *
    */
    public function decrement($column, $value=1){
        $data = array($column => $column.'-'.$value);

        return $this->update($data, true, false);
    }

    /*
    * Truncate.
    *
    */
    public function truncate(){
        return $this->table($this->table)->delete();
    }

    /**
     * @param bool $type
     *
     * @return mixed|string
     */
    public function delete($type = false)
    {
        $query = 'DELETE FROM ' . $this->table;

        if (!is_null($this->where)) {
            $query .= ' WHERE ' . $this->where;
        }

        if (!is_null($this->orderBy)) {
            $query .= ' ORDER BY ' . $this->orderBy;
        }

        if (!is_null($this->limit)) {
            $query .= ' LIMIT ' . $this->limit;
        }

        if ($query === 'DELETE FROM ' . $this->table) {
            $query = 'TRUNCATE TABLE ' . $this->table;
        }

        return $type === true ? $query : $this->query($query, false);
    }

     /**
     * @param string $value
     * @param string|null $primary_key
     *
     * @return mixed|string
     */
     public function find($value, $primary_key='')
     {
         // primary key
         if(empty($primary_key)){
             $primary_key = $this->primary_key;
         }
 
         // return
        return $this->where($primary_key, $value)->first();
     }

    /**
     */
    public function findOrFail($value, $primary_key='')
    {
        // Result
        $result = $this->find($value, $primary_key);

        // Fail
        if(!$result->data){
            throw new \Exception("Not found any rows.");
        }

        // Return data
        return $result;
    }


    /**
     * Get next auto increment
     * 
     * @return integer
     */
    public function nextId()
    {
        return $this->query("SHOW TABLE STATUS LIKE ?", [$this->table])->fetch()->Auto_increment;
    }
    
    
}