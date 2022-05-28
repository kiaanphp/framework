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
| Uses
|---------------------------------------------------
*/
use PDO;
use Kiaan\Database\DB\Result;

/*
|---------------------------------------------------
| Excute trait
|---------------------------------------------------
*/
trait ExcuteTrait {

    /**
    * Data
    * Set data
    */
    public function data($data)
    {          
        $result = json_decode(json_encode($data));
        $count = sizeof($data);

        return new Result($this, $result, $this->primary_key, '', $count);
    }

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
            $count = count($this->result);
        }else{
            $count = count(array($this->result));
        }

        return new Result($this, $this->result, $this->primary_key, '', $count);
    }
    /**
     * @param bool|string $type
     * @param string|null $argument
     *
     * @return mixed|string
     */
    public function get($type = null, $argument = null)
    {
        $query = 'SELECT ' . $this->select . ' FROM ' . $this->table;

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
        $count = count($this->result);

        return new Result($this, $this->result, $this->primary_key, '', $count);
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

        // Count
        $count = count($this->result);

        return new Result($this, $this->result, $this->primary_key, $page, $count);
    }

    /**
     * @param string      $field
     * @param string|null $name
     *
     * @return $this
     */
    public function max($field, $name = null)
    {
        $column = 'MAX(' . $field . ')' . (!is_null($name) ? ' AS ' . $name : '');
        $this->optimizeSelect($column);

        return $this;
    }

    /**
     * @param string      $field
     * @param string|null $name
     *
     * @return $this
     */
    public function min($field, $name = null)
    {
        $column = 'MIN(' . $field . ')' . (!is_null($name) ? ' AS ' . $name : '');
        $this->optimizeSelect($column);

        return $this;
    }

    /**
     * @param string      $field
     * @param string|null $name
     *
     * @return $this
     */
    public function sum($field, $name = null)
    {
        $column = 'SUM(' . $field . ')' . (!is_null($name) ? ' AS ' . $name : '');
        $this->optimizeSelect($column);

        return $this;
    }

    /**
     * @param string      $field
     * @param string|null $name
     *
     * @return $this
     */
    public function count($field, $name = null)
    {
        $column = 'COUNT(' . $field . ')' . (!is_null($name) ? ' AS ' . $name : '');
        $this->optimizeSelect($column);

        return $this;
    }

       /**
     * @param string      $field
     * @param string|null $name
     *
     * @return $this
     */
    public function avg($field, $name = null)
    {
        $column = 'AVG(' . $field . ')' . (!is_null($name) ? ' AS ' . $name : '');
        $this->optimizeSelect($column);

        return $this;
    }

    /**
     * @param array $data
     * @param bool  $type
     *
     * @return bool|string|int|null
     */
    public function insert(array $data, $type = false)
    {
        $query = 'INSERT INTO ' . $this->table;

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
            $this->insertId = $this->pdo->lastInsertId();
            return $this->insertId();
        }

        return false;
    }

    /**
     * @param array $data
     * @param bool  $type
     *
     * @return mixed|string
     */
    public function update(array $data, $type = false)
    {
        $query = 'UPDATE ' . $this->table . ' SET ';
        $values = [];

        foreach ($data as $column => $val) {
            $values[] = $column . '=' . $this->escape($val);
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
         // table
         $table = $this->table;
 
         // primary key
         if(empty($primary_key)){
             $primary_key = $this->primary_key;
         }
 
         // return
        return $this->where($primary_key, $value)->first();
     }

}