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
namespace Kiaan\Database\Schema;

/*
|---------------------------------------------------
| Uses
|---------------------------------------------------
*/
use PDO;
use PDOException;
use Exception;
use Kiaan\Database\Schema\CreateTable;

/*
|---------------------------------------------------
| Functions trait
|---------------------------------------------------
*/
trait FunctionsTrait{
    
    /**
     * Get driver
     *
     */
    public function driver()
    {
       return $this->connectClass->getAttribute(PDO::ATTR_DRIVER_NAME);
    }

    /**
     * Get server info
     *
     */
    public function server()
    {
       return $this->connectClass->getAttribute(PDO::ATTR_SERVER_INFO);
    }

    /**
     * Get version
     *
     */
    public function ver()
    {
       return $this->connectClass->getAttribute(PDO::ATTR_SERVER_VERSION);
    }

    /**
     * Get the name of database
     *
     */
    public function database()
    {
        $sql = $this->sqlClass->sql($this->driver(), 'database'); 
        $execute = $this->execute($sql['sql'], $sql['parms'], 'num');

        return array_column($execute, '0')[0];
    }

    /**
     * Get name of databases
     *
     */
    public function databases()
    {
        $sql = $this->sqlClass->sql($this->driver(), 'databases'); 
        $execute = $this->execute($sql['sql'], $sql['parms'], 'num');

        return array_column($execute, '0');
    }

    /**
     * Create new databases
     *
     */
    public function createDatabase($database)
    {
        $sql = $this->sqlClass->sql($this->driver(), 'createDatabase', $database); 
        $execute = $this->execute($sql['sql'], $sql['parms'], 'num');

        return true;
    }

    /**
     * Delete databases
     *
     */
    public function deleteDatabase($database)
    {
        $sql = $this->sqlClass->sql($this->driver(), 'deleteDatabase', $database); 
        $execute = $this->execute($sql['sql'], $sql['parms'], 'num');

        return true;
    }

    /**
     * has database
     *
     */
    public function hasDatabase($database)
    {
        $databases = $this->databases();

        if (in_array($database, $databases))
        {
           return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Get the list of the tables
     *
     */
    public function tables()
    {
        $sql = $this->sqlClass->sql($this->driver(), 'tables');
        $execute = $this->execute($sql['sql'], $sql['parms'], 'num');
        
        return array_column($execute, '0');
    }

    /**
     * Has table
     *
     */
    public function hasTable($table)
    {
        $sql = $this->sqlClass->sql($this->driver(), 'hasTable', $table);
        $execute = $this->execute($sql['sql'], $sql['parms'], 'assoc');

        if(empty($execute)){
            return false;
        }else{
            return true;
        }
    }

    /**
     * Create new table
     *
     */
    public function createTable($table)
    {
        CreateTable::$DbClass = $this;
        CreateTable::$sqlClass = $this->sqlClass;
        CreateTable::$sqlDriver = $this->driver();
        
        return (new CreateTable)::init($table);
    }

    /**
     * Rename Table
     *
     */
    public function renameTable($old, $new)
    {
        $args = ["old" => $old, "new" => $new];
        $sql = $this->sqlClass->sql($this->driver(), 'renameTable', $args);
        $execute = $this->execute($sql['sql'], $sql['parms']);

        return $execute;
    }

    /**
     * Clean Table
     *
     */
    public function CleanTable($table)
    {
        $sql = $this->sqlClass->sql($this->driver(), 'CleanTable', $table);
        $execute = $this->execute($sql['sql'], $sql['parms']);

        return $execute;
    }

    /**
     * Delete Table
     *
     */
    public function deleteTable($table)
    {
        $sql = $this->sqlClass->sql($this->driver(), 'deleteTable', $table);
        $execute = $this->execute($sql['sql'], $sql['parms']);

        return $execute;
    }

    /**
     * Columns
     *
     */
    public function columns($table)
    {
        $sql = $this->sqlClass->sql($this->driver(), 'columns', $table);
        $execute = $this->execute($sql['sql'], $sql['parms']);

        return $execute;
    }

    /**
     * Has column
     *
    */
    public function hasColumn($table, $column)
    {
        $args = ["table" => $table, "column" => $column];

        $sql = $this->sqlClass->sql($this->driver(), 'hasColumn', $args);
        $execute = $this->execute($sql['sql'], $sql['parms'], 'assoc');

        if(empty($execute)){
            return false;
        }else{
            return true;
        }
    }

    /**
     * Delete column
     *
     */
    public function deleteColumn($table, $columns=[])
    {
        $args = ["table" => $table, "columns" => $columns];
        $sql = $this->sqlClass->sql($this->driver(), 'deleteColumn', $args);
        $execute = $this->execute($sql['sql'], $sql['parms']);

        return $execute;
    }

}