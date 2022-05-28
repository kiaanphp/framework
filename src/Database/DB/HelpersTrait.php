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
use PDO;
use PDOException;

/*
|---------------------------------------------------
| Helpers trait
|---------------------------------------------------
*/
trait HelpersTrait {

    /**
     * Reset
     * 
     * @return void
     */
    protected function reset()
    {
        $this->select = '*';
        $this->from = null;
        $this->where = null;
        $this->limit = null;
        $this->offset = null;
        $this->orderBy = null;
        $this->groupBy = null;
        $this->having = null;
        $this->join = null;
        $this->grouped = false;
        $this->numRows = 0;
        $this->insertId = null;
        $this->query = null;
        $this->error = null;
        $this->result = [];
        $this->transactionCount = 0;
    }

    /**
     * @param int $perPage
     * @param int $page
     *
     * @return $this
     */
    protected function pagination($perPage, $page)
    {
        $this->limit = $perPage;
        $this->offset = (($page > 0 ? $page : 1) - 1) * $perPage;

        return $this;
    }
    
    /**
     * getFetchType
     * 
     * @param  $type
     *
     * @return int
     */
    protected function getFetchType($type)
    {
        return $type === 'class'
            ? PDO::FETCH_CLASS
            : ($type === 'array'
                ? PDO::FETCH_ASSOC
                : PDO::FETCH_OBJ);
    }

    /**
     * optimizeSelect
     * 
     * Optimize Selected fields for the query
     *
     * @param string $fields
     *
     * @return void
     */
    private function optimizeSelect($fields)
    {
        $this->select = $this->select === '*'
            ? $fields
            : $this->select . ', ' . $fields;
    }

    /**
     * @throw PDOException
     */
    public function error()
    {
        if ($this->debug === true) {
            if (php_sapi_name() === 'cli') {
                die("Query: " . $this->query . PHP_EOL . "Error: " . $this->error . PHP_EOL);
            }

            $msg = '<h1>Database Error</h1>';
            $msg .= '<h4>Query: <em style="font-weight:normal;">"' . $this->query . '"</em></h4>';
            $msg .= '<h4>Error: <em style="font-weight:normal;">' . $this->error . '</em></h4>';
            die($msg);
        }

        throw new PDOException($this->error . '. (' . $this->query . ')');
    }
    
    /**
     * @param $data
     *
     * @return string
     */
    public function escape($data)
    {
        return $data === null ? 'NULL' : (
            is_int($data) || is_float($data) ? $data : $this->pdo->quote($data)
        );
    }

    /**
     * @return int
     */
    public function queryCount()
    {
        return $this->queryCount;
    }

}