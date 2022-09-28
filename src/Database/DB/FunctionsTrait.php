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
use Closure;

/*
|---------------------------------------------------
| Functions trait
|---------------------------------------------------
*/
trait FunctionsTrait {

    /**
     * @param $table
     *
     * @return $this
     */
    public function table($table)
    {
        // Reset
        $this->reset();

        // Query
        if (is_array($table)) {
            $from = '';
            foreach ($table as $key) {
                $from .= $this->prefix . $key . ', ';
            }
            $this->table = rtrim($from, ', ');
        } else {
            if (strpos($table, ',') > 0) {
                $tables = explode(',', $table);
                foreach ($tables as $key => &$value) {
                    $value = $this->prefix . ltrim($value);
                }
                $this->table = implode(', ', $tables);
            } else {
                $this->table = $this->prefix . $table;
            }
        }

        // Cache some query
        $this->cache_some_query();
        
        // Return
        return clone($this);
    }

    /**
     * @param array|string $fields
     *
     * @return $this
     */
    public function select($fields)
    {
        $select = is_array($fields) ? implode(', ', $fields) : $fields;
        $this->optimizeSelect($select);
        return clone($this);
    }

    /**
     * @param string      $table
     * @param string|null $field1
     * @param string|null $operator
     * @param string|null $field2
     * @param string      $type
     *
     * @return $this
     */
    public function join($table, $field1 = null, $operator = null, $field2 = null, $type = '')
    {
        $on = $field1;
        $table = $this->prefix . $table;

        if (!is_null($operator)) {
            $on = !in_array($operator, $this->operators)
                ? $field1 . ' = ' . $operator . (!is_null($field2) ? ' ' . $field2 : '')
                : $field1 . ' ' . $operator . ' ' . $field2;
        }

        $this->join = (is_null($this->join))
            ? ' ' . $type . 'JOIN' . ' ' . $table . ' ON ' . $on
            : $this->join . ' ' . $type . 'JOIN' . ' ' . $table . ' ON ' . $on;

        return clone($this);
    }

    /**
     * @param string $table
     * @param string $field1
     * @param string $operator
     * @param string $field2
     *
     * @return $this
     */
    public function innerJoin($table, $field1, $operator = '', $field2 = '')
    {
        return $this->join($table, $field1, $operator, $field2, 'INNER ');
    }

    /**
     * @param string $table
     * @param string $field1
     * @param string $operator
     * @param string $field2
     *
     * @return $this
     */
    public function leftJoin($table, $field1, $operator = '', $field2 = '')
    {
        return $this->join($table, $field1, $operator, $field2, 'LEFT ');
    }

    /**
     * @param string $table
     * @param string $field1
     * @param string $operator
     * @param string $field2
     *
     * @return $this
     */
    public function rightJoin($table, $field1, $operator = '', $field2 = '')
    {
        return $this->join($table, $field1, $operator, $field2, 'RIGHT ');
    }

    /**
     * @param string $table
     * @param string $field1
     * @param string $operator
     * @param string $field2
     *
     * @return $this
     */
    public function fullOuterJoin($table, $field1, $operator = '', $field2 = '')
    {
        return $this->join($table, $field1, $operator, $field2, 'FULL OUTER ');
    }

    /**
     * @param string $table
     * @param string $field1
     * @param string $operator
     * @param string $field2
     *
     * @return $this
     */
    public function leftOuterJoin($table, $field1, $operator = '', $field2 = '')
    {
        return $this->join($table, $field1, $operator, $field2, 'LEFT OUTER ');
    }

    /**
     * @param string $table
     * @param string $field1
     * @param string $operator
     * @param string $field2
     *
     * @return $this
     */
    public function rightOuterJoin($table, $field1, $operator = '', $field2 = '')
    {
        return $this->join($table, $field1, $operator, $field2, 'RIGHT OUTER ');
    }

    
    /**
     * @param array|string $where
     * @param string       $operator
     * @param string       $val
     * @param string       $type
     * @param string       $andOr
     *
     * @return $this
     */
    public function where($where, $operator = null, $val = null, $type = '', $andOr = 'AND')
    {
        if (is_array($where) && !empty($where)) {
            $_where = [];
            foreach ($where as $column => $data) {
                $_where[] = $type . $column . '=' . $this->escape($data);
            }
            $where = implode(' ' . $andOr . ' ', $_where);
        } else {
            if (is_null($where) || empty($where)) {
                return clone($this);
            }

            if (is_array($operator)) {
                $params = explode('?', $where);
                $_where = '';
                foreach ($params as $key => $value) {
                    if (!empty($value)) {
                        $_where .= $type . $value . (isset($operator[$key]) ? $this->escape($operator[$key]) : '');
                    }
                }
                $where = $_where;
            } elseif (!in_array($operator, $this->operators) || $operator == false) {
                $where = $type . $where . ' = ' . $this->escape($operator);
            } else {
                $where = $type . $where . ' ' . $operator . ' ' . $this->escape($val);
            }
        }

        if ($this->grouped) {
            $where = '(' . $where;
            $this->grouped = false;
        }

        $this->where = is_null($this->where)
            ? $where
            : $this->where . ' ' . $andOr . ' ' . $where;

        return clone($this);
    }

    /**
     * @param array|string $where
     * @param string|null  $operator
     * @param string|null  $val
     *
     * @return $this
     */
    public function orWhere($where, $operator = null, $val = null)
    {
        return $this->where($where, $operator, $val, '', 'OR');
    }

    /**
     * @param array|string $where
     * @param string|null  $operator
     * @param string|null  $val
     *
     * @return $this
     */
    public function notWhere($where, $operator = null, $val = null)
    {
        return $this->where($where, $operator, $val, 'NOT ', 'AND');
    }

    /**
     * @param array|string $where
     * @param string|null  $operator
     * @param string|null  $val
     *
     * @return $this
     */
    public function orNotWhere($where, $operator = null, $val = null)
    {
        return $this->where($where, $operator, $val, 'NOT ', 'OR');
    }

    /**
     * @param string $where
     * @param bool   $not
     *
     * @return $this
     */
    public function whereNull($where, $not = false)
    {
        $where = $where . ' IS ' . ($not ? 'NOT' : '') . ' NULL';
        $this->where = is_null($this->where) ? $where : $this->where . ' ' . 'AND ' . $where;

        return clone($this);
    }

    /**
     * @param string $where
     *
     * @return $this
     */
    public function whereNotNull($where)
    {
        return $this->whereNull($where, true);
    }

    /**
     * @param string $id
     *
     * @return $this
     */
    public function whereId($id)
    {
        return $this->where($this->primary_key, $id);
    }

    /**
     * @param Closure $obj
     *
     * @return $this
     */
    public function grouped(Closure $obj)
    {
        $this->grouped = true;
        call_user_func_array($obj, [$this]);
        $this->where .= ')';

        return clone($this);
    }

    /**
     * @param string $field
     * @param array  $keys
     * @param string $type
     * @param string $andOr
     *
     * @return $this
     */
    public function in($field, array $keys, $type = '', $andOr = 'AND')
    {
        if (is_array($keys)) {
            $_keys = [];
            foreach ($keys as $k => $v) {
                $_keys[] = is_numeric($v) ? $v : $this->escape($v);
            }
            $where = $field . ' ' . $type . 'IN (' . implode(', ', $_keys) . ')';

            if ($this->grouped) {
                $where = '(' . $where;
                $this->grouped = false;
            }

            $this->where = is_null($this->where)
                ? $where
                : $this->where . ' ' . $andOr . ' ' . $where;
        }

        return clone($this);
    }

    /**
     * @param string $field
     * @param array  $keys
     *
     * @return $this
     */
    public function notIn($field, array $keys)
    {
        return $this->in($field, $keys, 'NOT ', 'AND');
    }

    /**
     * @param string $field
     * @param array  $keys
     *
     * @return $this
     */
    public function orIn($field, array $keys)
    {
        return $this->in($field, $keys, '', 'OR');
    }

    /**
     * @param string $field
     * @param array  $keys
     *
     * @return $this
     */
    public function orNotIn($field, array $keys)
    {
        return $this->in($field, $keys, 'NOT ', 'OR');
    }

    /**
     * @param string     $field
     * @param string|int $value1
     * @param string|int $value2
     * @param string     $type
     * @param string     $andOr
     *
     * @return $this
     */
    public function between($field, $value1, $value2, $type = '', $andOr = 'AND')
    {
        $where = '(' . $field . ' ' . $type . 'BETWEEN ' . ($this->escape($value1) . ' AND ' . $this->escape($value2)) . ')';
        if ($this->grouped) {
            $where = '(' . $where;
            $this->grouped = false;
        }

        $this->where = is_null($this->where)
            ? $where
            : $this->where . ' ' . $andOr . ' ' . $where;

        return clone($this);
    }

    /**
     * @param string     $field
     * @param string|int $value1
     * @param string|int $value2
     *
     * @return $this
     */
    public function notBetween($field, $value1, $value2)
    {
        return $this->between($field, $value1, $value2, 'NOT ', 'AND');
    }

    /**
     * @param string     $field
     * @param string|int $value1
     * @param string|int $value2
     *
     * @return $this
     */
    public function orBetween($field, $value1, $value2)
    {
        return $this->between($field, $value1, $value2, '', 'OR');
    }

    /**
     * @param string     $field
     * @param string|int $value1
     * @param string|int $value2
     *
     * @return $this
     */
    public function orNotBetween($field, $value1, $value2)
    {
        return $this->between($field, $value1, $value2, 'NOT ', 'OR');
    }


    /**
     * @param string $field
     * @param string $data
     * @param string $type
     * @param string $andOr
     *
     * @return $this
     */
    public function like($field, $data, $type = '', $andOr = 'AND')
    {
        $like = $this->escape($data);
        $where = $field . ' ' . $type . 'LIKE ' . $like;

        if ($this->grouped) {
            $where = '(' . $where;
            $this->grouped = false;
        }

        $this->where = is_null($this->where)
            ? $where
            : $this->where . ' ' . $andOr . ' ' . $where;

        return clone($this);
    }

    /**
     * @param string $field
     * @param string $data
     *
     * @return $this
     */
    public function orLike($field, $data)
    {
        return $this->like($field, $data, '', 'OR');
    }

    /**
     * @param string $field
     * @param string $data
     *
     * @return $this
     */
    public function notLike($field, $data)
    {
        return $this->like($field, $data, 'NOT ', 'AND');
    }

    /**
     * @param string $field
     * @param string $data
     *
     * @return $this
     */
    public function orNotLike($field, $data)
    {
        return $this->like($field, $data, 'NOT ', 'OR');
    }

    /**
     * @param int      $limit
     * @param int|null $limitEnd
     *
     * @return $this
     */
    public function limit($limit, $limitEnd = null)
    {
        $this->limit = !is_null($limitEnd)
            ? $limit . ', ' . $limitEnd
            : $limit;

        return clone($this);
    }

    /**
     * @param int $offset
     *
     * @return $this
     */
    public function offset($offset)
    {
        $this->offset = $offset;

        return clone($this);
    }

    /**
     * @param string      $orderBy
     * @param string|null $orderDir
     *
     * @return $this
     */
    public function orderBy($orderBy, $orderDir = null)
    {
        if (!is_null($orderDir)) {
            $this->orderBy = $orderBy . ' ' . strtoupper($orderDir);
        } else {
            $this->orderBy = stristr($orderBy, ' ') || strtolower($orderBy) === 'rand()'
                ? $orderBy
                : $orderBy . ' ASC';
        }

        return clone($this);
    }

    /**
     * @return $this
     */ 
    public function idAsc(){
        return $this->orderBy($this->primary_key, "asc");
    }

    /**
     * @return $this
     */ 
    public function idDesc(){
        return $this->orderBy($this->primary_key, "desc");
    }

    /**
     * @return $this
     */ 
    public function orderByRand(){
        return $this->orderBy("rand()");
    }

    /**
     * @param string|array $groupBy
     *
     * @return $this
     */
    public function groupBy($groupBy)
    {
        $this->groupBy = is_array($groupBy) ? implode(', ', $groupBy) : $groupBy;

        return clone($this);
    }

    /**
     * @param string            $field
     * @param string|array|null $operator
     * @param string|null       $val
     *
     * @return $this
     */
    public function having($field, $operator = null, $val = null)
    {
        if (is_array($operator)) {
            $fields = explode('?', $field);
            $where = '';
            foreach ($fields as $key => $value) {
                if (!empty($value)) {
                    $where .= $value . (isset($operator[$key]) ? $this->escape($operator[$key]) : '');
                }
            }
            $this->having = $where;
        } elseif (!in_array($operator, $this->operators)) {
            $this->having = $field . ' > ' . $this->escape($operator);
        } else {
            $this->having = $field . ' ' . $operator . ' ' . $this->escape($val);
        }

        return clone($this);
    }

   /**
     */
    public function softDelete()
    {
        return $this->update(array($this->soft_delete_at => date("Y-m-d h:i:sa")));
    }

    /**
     */
    public function restoreDelete()
    {
        return $this->update(array($this->soft_delete_at => null));
    }

    /**
     */
    public function hideArchived()
    {
        return $this->whereNull($this->soft_delete_at);
    }

    /**
     */
    public function onlyArchived()
    {
        return $this->whereNotNull($this->soft_delete_at);
    }
   
    /**
     * Getter method.
     * 
    */
    public function __getter()
    {
        return false;
    }

    /**
     * Setter method.
     * 
    */
    public function __setter($item)
    {
        return false;
    } 
}