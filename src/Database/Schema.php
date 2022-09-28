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
| Schema
|---------------------------------------------------
*/
class Schema {
    
    /*
    * Global traits
    *
    */
    use \Kiaan\Application\Resources\Global\ExtendingTrait;

    /*
    * Connect class
    */
    public $connectClass;

    /*
    * Sql class
    */
    public $sqlClass;

    /*
    * Migrations
    */
    public $migrations;

    /*
    * Migrations method
    */
    public $migrations_method = 'migration';
    
    /*
    * Rollback method
    */
    public $rollback_method = 'rollback';

    
    /*
    * Migrations list
    */
    public $migrations_list = array();

    /*
    * Seeds list
    */
    public $seeds_list = array();

    /*
    * Seeds method
    */
    public $seeds_method = 'handle';

    /*
    * Migrations
    */
    public $seeds;

    /**
     * Get connect
    */
    public function getConnect()
    {
        return $this->connectClass;
    }

    /**
     * Set connect
    */
    public function setConnect($pdo)
    {
        $this->sqlClass = (new Schema\Sql);
        $this->connectClass = $pdo;
        $this->sqlClass->drive = $this->driver();
    }
    
    /**
     * Get migration
    */
    public function getMigration()
    {
        return json_decode(json_encode((object) $this->migrations), false);
    }

    /**
     * Set migration
    */
    public function setMigration($namespace, $table)
    {
        $this->migrations = [
            "namespace" => $namespace,
            "table" => $table
        ];
    }

    /**
     * Set migration path
    */
    public function setMigrationPath($path)
    {
        $this->migrations['path'] = $path;
    }

    /**
     * Get seed
    */
    public function getSeed()
    {
        return json_decode(json_encode((object) $this->seeds), false);
    }

    /**
     * Set seed
    */
    public function setSeed($namespace)
    {
        $this->seeds = [
            "namespace" => $namespace,
        ];
    }
    
    /**
     * Set seed path
    */
    public function setSeedPath($path)
    {
        $this->seeds['path'] = $path;
    }

    /*
    * Add to migration list
    *
    */
    public function migrations(array $migrations){
        $this->migrations_list = array_merge($this->migrations_list, $migrations);
    }

    /*
    * Add to seeds list
    *
    */
    public function seeds(array $seeds){
        $this->seeds_list = array_merge($this->seeds_list, $seeds);
    }

    /*
    * Run migrate
    *
    */
    public function runMigrate($class_name=null){
        $this->generate_migration($class_name, $this->migrations_method, $this->migrations_list, $this->getMigration()->namespace);
        
        return true;
    }

    /*
    * Run migrate rollback
    *
    */
    public function runRollback($class_name=null){
        $this->generate_migration($class_name, $this->rollback_method, $this->migrations_list, $this->getMigration()->namespace);

        return true;
    }

    /*
    * Run migrate
    *
    */
    public function runSeeds($class_name=null){
        $this->generate_migration($class_name, $this->seeds_method, $this->seeds_list, $this->getSeed()->namespace);
        
        return true;
    }

    /*
    * Generate migration class prepare
    *
    */
    protected function generate_migration($class_name, $method, $in_list, $namespace){
        if(!is_null($class_name)){
            if(is_string($class_name)){
                $list = explode(",", $class_name);
            }
        }else{
            $list = array_keys($in_list);
        }

        foreach ($list as $item) {
            $item = trim($item);

            if(!isset($in_list[$item])){
                throw new \Exception("'$item' not found in list.");
            }

            $class = $this->generate_migration_class_prepare($item, $namespace);
            $class->{$method}();
        }

        return true;
    }

    /*
    * Generate migration class prepare
    *
    */
    protected function generate_migration_class_prepare($class_name, $prefix){
        if(!class_exists($class_name)){
            $class = $prefix . '\\' . $class_name;
        }

        return new $class;
    }

    /**
     * execute Query
     *
    */
    public function execute($sql, $parms=[], $fetch="obj")
    {
        // fetch type
        switch ($fetch) {
            case 'obj':
                $fetch = PDO::FETCH_OBJ;
                break;

            case 'num':
                $fetch = PDO::FETCH_NUM;
                break;
                
            case 'name':
                $fetch = PDO::FETCH_NAMED;
                break;
                
            case 'lazy':
                $fetch = PDO::FETCH_LAZY;
                break;
                
            case 'into':
                $fetch = PDO::FETCH_INTO;
                break;

            case 'class':
                $fetch = PDO::FETCH_CLASS;
                break;

            case 'bound':
                $fetch = PDO::FETCH_BOUND;
                break;

            case 'both':
                $fetch = PDO::FETCH_BOTH;
                break;

            case 'assoc':
                $fetch = PDO::FETCH_ASSOC;
                break;

            default:
                $fetch = PDO::FETCH_OBJ;
                break;
        }

        // excute
        $excute = $this->connectClass->prepare($sql);
        $excute->execute($parms); 

        try {
            $excute = $excute->fetchAll($fetch);
        } catch (\Throwable $th) {
        }

        return $excute;
    }

    /**
     * Get driver
     *
     */
    public function driver()
    {
        try {
            $driver = $this->connectClass->getAttribute(PDO::ATTR_DRIVER_NAME);
        } catch (\Throwable $th) {
            $driver = null;
        }
        
       return $driver;
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
     * Get the name of the currently used database
    */
    public function database()
    {
        $sql = $this->sqlClass->generate('database');

        return $this->execute($sql);
    }

    /**
     * Get the name of databases
    */
    public function databases()
    {
        $sql = $this->sqlClass->generate('databases');

        return $this->execute($sql);
    }
       
    /**
     * Create new database
    */
    public function createDatabase($database)
    {
        $sql = $this->sqlClass->generate('createDatabase', $database);
        
        $this->execute($sql);

        return true;
    }
    
    /**
     * Delete database
    */
    public function deleteDatabase($database)
    {
        $sql = $this->sqlClass->generate('deleteDatabase', $database);

        $this->execute($sql);

        return true;
    }

     /**
     * Get the list of the tables
     *
     */
    public function tables()
    {
        $sql = $this->sqlClass->generate('tables');

        return $this->execute($sql);
    }
    
     /**
     * has table
     *
     */
    public function hasTable($table)
    {
        $sql = $this->sqlClass->generate('hasTable', $table);

        return count($this->execute($sql)) == 1;
    }

    /**
     * Delete Table
     *
     */
    public function deleteTable($table) {
        $sql = $this->sqlClass->generate('deleteTable', $table);

        $this->execute($sql);

        return true;
    }

    /**
     * Rename Table
     *
     */
    public function renameTable($old, $new) {
        $sql = $this->sqlClass->generate('renameTable', $old, $new);

        $this->execute($sql);

        return true;
    }

    /**
     * Clean Table
     *
     */
    public function cleanTable($table) {
        $sql = $this->sqlClass->generate('cleanTable', $table);

        $this->execute($sql);

        return true;
    }

    /**
     * Create New Table
     *
     */
    public function createTable($table) {
        return $this->sqlClass->generate('createTable', $this, $table);
    }

    /**
     * Foreign keys
     *
     */
    public function foreign($foreign_table, $primary_table=null) {
        return $this->sqlClass->generate('foreign', $this, $foreign_table, $primary_table);
    }

    /**
     * Create column
     *
     */
    public function createColumn($table) {
        return $this->sqlClass->generate('createColumn', $this, $table);
    }

    /**
     * Columns
     *
     */
    public function columns($table)
    {
        $sql = $this->sqlClass->generate('columns', $table);

        return $this->execute($sql);
    }

    /**
     * Has Column
     *
     */
    public function hasColumn($table, $columns)
    {
        $sql = $this->sqlClass->generate('hasColumn', $table, $columns);

        return count($this->execute($sql)) == 1;
    }

    /**
     * Delete column
     *
     */
    public function deleteColumn($table, $columns) {
        $sql = $this->sqlClass->generate('deleteColumn', $table, $columns);
        
        $this->execute($sql);

        return true;
    }

    /**
     * Rename column
     *
     */
    public function renameColumn($table, $old, $new) {
        $sql = $this->sqlClass->generate('renameColumn', $table, $old, $new);
        
        $this->execute($sql);

        return true;
    }

}