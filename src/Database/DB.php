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
namespace Kiaan\Database;

/*
|---------------------------------------------------
| Uses
|---------------------------------------------------
*/
use PDO;

/*
|---------------------------------------------------
| DB
|---------------------------------------------------
*/
class DB implements \IteratorAggregate {

    /**
    * Traits
    *
    */
    use DB\ConnectionTrait;
    use DB\HelpersTrait;
    use DB\FunctionsTrait;
    use DB\ExcuteTrait;
    use DB\ResultTrait;
    
    /*
    * Global traits
    *
    */
    use \Kiaan\Application\Resources\Global\ExtendingTrait;
         
    /**
    * Pdo
    *
    */
    public $pdo = null;
    public static $pdo_static = null;

    /*
    * Model configuration
    *
    */
    public $model_config;
    
    /**
     * @var mixed Query variables
     */
    protected $select = '*';
    protected $distinct = '';
    protected $primary_key = 'id';
    protected $table = null;
    protected $where = null;
    protected $limit = null;
    protected $offset = null;
    protected $join = null;
    protected $orderBy = null;
    protected $groupBy = null;
    protected $having = null;
    protected $grouped = false;
    protected $numRows = 0;
    protected $count = 0;
    protected $insertId = null;
    protected $query = null;
    protected $error = null;
    protected $result = [];
    protected $data = [];
    protected $prefix = null;
    protected $cache_some_query = [
        'table' => null,
        'where' => null,
        'limit' => null,
        'orderBy' => null,
    ];
    
    /**
     * Soft delete at field
    */
    protected $soft_delete_at = 'soft_delete_at';

    /**
     * pagination
    */
    protected $page;

    /**
     * @var array SQL operators
     */
    protected $operators = ['=', '!=', '<', '>', '<=', '>=', '<>'];

    /**
     * @var Cache|null
     */
    protected $cache = null;

    /**
     * @var int Total query count
     */
    protected $queryCount = 0;

    /**
     * @var bool
     */
    protected $debug = true;

    /**
     * @var int Total transaction count
     */
    protected $transactionCount = 0;

    /*
    * Getter
    *
    */
    public $only = array();
    public $hidden = array();

    /**
     * Set PDO
     */
    public function setPdo($pdo=null)
    {
        if($pdo==null){
            return $this->pdo = self::$pdo_static;
        }

        // PDO class
        $this->pdo = $pdo;

        // PDO Static
        self::$pdo_static = $pdo;
    }

    /**
     * Get PDO
     */
    public function getPdo()
    {
        return $this->pdo;
    }

    /**
     * Set model
     */
    public function setModel($namespace, $path)
    {
        return $this->model_config = array("namespace" => $namespace, "path" => $path);
    }

    /**
     * Get model
     */
    public function getModel()
    {
        return (object) $this->model_config;
    }

    /**
     * Destruct
     * 
     * @return void
     */
    public function __destruct()
    {
        $this->pdo = null;
    }

    /**
     * @return string|null
     */
    public function getQuery()
    {
        return $this->query;
    }
    
    /**
     * @return mixed
     */
    public function exec()
    {
        if (is_null($this->query)) {
            return null;
        }

        $query = $this->pdo->exec($this->query);
        if ($query === false) {
            $this->error = $this->pdo->errorInfo()[2];
            //$this->error();
        }

        return $query;
    }

    /**
     * @param string $type
     * @param string $argument
     * @param bool   $all
     *
     * @return mixed
     */
    public function fetch($type = null, $argument = null, $all = false)
    {
        if (is_null($this->query)) {
            return null;
        }

        $query = $this->pdo->query($this->query);
        if (!$query) {
            $this->error = $this->pdo->errorInfo()[2];
        }

        $type = $this->getFetchType($type);
        if ($type === PDO::FETCH_CLASS) {
            $query->setFetchMode($type, $argument);
        } else {
            $query->setFetchMode($type);
        }

        $result = $all ? $query->fetchAll() : $query->fetch();
        $this->size = is_array($result) ? count($result) : 1;

        // Result
        return $result;
    }

    /**
     * @param string $type
     * @param string $argument
     *
     * @return mixed
     */
    public function fetchAll($type = null, $argument = null)
    {
        return $this->fetch($type, $argument, true);
    }

    /**
     * @return int
     */
    public function size()
    {
        return $this->size;
    }

    /**
     * @return int|null
     */
    public function lastId()
    {
        return $this->insertId;
    }

    /**
     * @return mixed
     */
    public function analyze()
    {
        return $this->query('ANALYZE TABLE ' . $this->table, false);
    }

    /**
     * @return mixed
     */
    public function check()
    {
        return $this->query('CHECK TABLE ' . $this->table, false);
    }

    /**
     * @return mixed
     */
    public function checksum()
    {
        return $this->query('CHECKSUM TABLE ' . $this->table, false);
    }

    /**
     * @return mixed
     */
    public function optimize()
    {
        return $this->query('OPTIMIZE TABLE ' . $this->table, false);
    }

    /**
     * @return mixed
     */
    public function repair()
    {
        return $this->query('REPAIR TABLE ' . $this->table, false);
    }

    /**
     * @return bool
     */
    public function transaction()
    {
        if (!$this->transactionCount++) {
            return $this->pdo->beginTransaction();
        }

        $this->pdo->exec('SAVEPOINT trans' . $this->transactionCount);
        return $this->transactionCount >= 0;
    }

    /**
     * @return bool
     */
    public function commit()
    {
        if (!--$this->transactionCount) {
            return $this->pdo->commit();
        }

        return $this->transactionCount >= 0;
    }

    /**
     * @return bool
     */
    public function rollBack()
    {
        if (--$this->transactionCount) {
            $this->pdo->exec('ROLLBACK TO trans' . ($this->transactionCount + 1));
            return true;
        }

        return $this->pdo->rollBack();
    }

    /*
    * Cache some query
    */
    protected function cache_some_query()
    {
        $this->cache_some_query = [
            'table' => $this->table,
            'where' => $this->where,
            'limit' => $this->limit,
            'orderBy' => $this->orderBy,
        ];
    }

     /**
     * @param        $query
     * @param bool   $all
     * @param string $type
     * @param string $argument
     *
     * @return self::class|mixed
     */
    public function query($query, $all = true, $type = null, $argument = null)
    {
        // Cache some query
        $this->cache_some_query();

        // Reset
        $this->reset();

        // Query
        if (is_array($all) || func_num_args() === 1) {
            $params = explode('?', $query);
            $newQuery = '';
            foreach ($params as $key => $value) {
                if (! empty($value)) {
                    $newQuery .= $value . (isset($all[$key]) ? $this->escape($all[$key]) : '');
                }
            }

            $this->query = $newQuery;
            return clone($this);
        }

        $this->query = preg_replace('/\s\s+|\t\t+/', ' ', trim($query));
        $str = false;
        foreach (['select', 'optimize', 'check', 'repair', 'checksum', 'analyze'] as $value) {
            if (stripos($this->query, $value) === 0) {
                $str = true;
                break;
            }
        }

        $type = $this->getFetchType($type);
        $cache = false;
        if (! is_null($this->cache) && $type !== PDO::FETCH_CLASS) {
            $cache = $this->cache->getCache($this->query, $type === PDO::FETCH_ASSOC);
        }

        if (! $cache && $str) {
            $sql = $this->pdo->query($this->query);

            if ($sql) {
                $this->numRows = $sql->rowCount();
                if (($this->numRows > 0)) {
                    if ($type === PDO::FETCH_CLASS) {
                        $sql->setFetchMode($type, $argument);
                    } else {
                        $sql->setFetchMode($type);
                    }
                    $this->result = $all ? $sql->fetchAll() : $sql->fetch();
                }

                if (! is_null($this->cache) && $type !== PDO::FETCH_CLASS) {
                    $this->cache->setCache($this->query, $this->result);
                }
                $this->cache = null;
            } else {
                $this->cache = null;
                $this->error = $this->pdo->errorInfo()[2];
            }
        } elseif ((! $cache && ! $str) || ($cache && ! $str)) {
            $this->cache = null;
            $this->result = $this->pdo->exec($this->query);

            if ($this->result === false) {
                $this->error = $this->pdo->errorInfo()[2];
            }
        } else {
            $this->cache = null;
            $this->result = $cache;
            $this->numRows = is_array($this->result) ? count($this->result) : ($this->result == '' ? 0 : 1);
        }

        // Count
        $this->queryCount++;

        // Return result
        return $this->result;
    }
    
    /**
    * Distinct
    *
    */
    public function distinct()
    {
          $this->distinct = 'DISTINCT ';

          return clone($this);
    }

    /**
    * @param string $primary_key
    *
    */
    public function key($primary_key)
    {
          // set primary key
          $this->primary_key = $primary_key;

          return clone($this);
    }

    /*
    * Get primary key
    */
    public function getPrimaryKey()
    {
        return $this->primary_key;
    }

    /*
    * Set primary key
    */
    public function setPrimaryKey($primary_key)
    {
        return $this->primary_key = $primary_key;
    }

}
