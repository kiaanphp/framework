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
namespace Kiaan\Config\Config;

/*
|---------------------------------------------------
| Config system Trait
|---------------------------------------------------
*/
trait ConfigSystemTrait
{

    /**
     *  PDO
     * 
    */
    protected $pdo;

    /**
     *  Table
     * 
    */
    protected $table;

    /**
     * Get PDO
     *
     */
    public function getPdo() {
        return $this->pdo;
    }

    /**
     * Set PDO
     *
     */
    public function setPdo($value) {
        return $this->pdo = $value;
    }

    /**
     * Get table
     *
    */
    public function getTable() {
        return $this->table;
    }

    /**
     * Set table
     *
    */
    public function setTable($value) {
        return $this->table = $value;
    }

}

