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
use Kiaan\Database\Schema\Sql;

/*
|---------------------------------------------------
| mysql
|---------------------------------------------------
*/
class mysql {

    /*
    * Columns type
    *
    */
    public $columnsType = [
        "id" => "BIGINT(20) AUTO_INCREMENT PRIMARY KEY",
        "bigInteger" => "BIGINT",
        "binary" => "BLOB",
        "boolean" => "BOOLEAN",
        "char" => "CHAR(:0)",
        "date" => "DATE",
        "dateTime" => "DATETIME",
        "decimal" => "DECIMAL(:0, :1)",
        "double" => "DOUBLE(:0, :1)",
        "enum" => "ENUM(:0)",
        "float" => "FLOAT",
        "increments" => "BIGINT(20) AUTO_INCREMENT PRIMARY KEY",
        "integer" => "INTEGER",
        "longText" => "LONGTEXT",
        "mediumInteger" => "MEDIUMINT",
        "mediumText" => "mediumText",
        "smallInteger" => "SMALLINT",
        "tinyInteger" => "TINYINT",
        "string" => "VARCHAR(:0)",
        "text" => "TEXT",
        "json" => "JSON",
        "time" => "TIME",
        "timestamp" => "TIMESTAMP",
    ];
    
    /*
    * Attributes type
    *
    */
    public $attributesType = [
        "primary" => "PRIMARY KEY",
        "auto" => "AUTO_INCREMENT",
        "unique" => "UNIQUE",
        "positive" => "unsigned",
        "updateCurrent" => "ON UPDATE CURRENT_TIMESTAMP",
        "current" => "DEFAULT CURRENT_TIMESTAMP",
        "nullable" => "NULL DEFAULT NULL",
        "notNullable" => "NOT NULL",
        "default" => "DEFAULT :0",
        "after" => "AFTER :0",
    ];

     /**
     * Get the name of the currently used database
     *
     */
    public function database()
    {
        // sql
        $sql = 'SELECT database()';

        return $sql;
    }

     /**
     * Get the name of databases
     *
     */
    public function databases()
    {        
        $sql = "SHOW DATABASES";

        return $sql;
    }

    /**
     * Create new databases
     *
     */
    public function createDatabase($database)
    {
        $sql = "CREATE DATABASE $database";

        return $sql;
    }

    /**
     * Delete database
     *
     */
    public function deleteDatabase($databases)
    {
        // Databases
        if(is_string($databases)){
            $databases = explode(",", $databases);
        }
        
        // Sql
        $sql = "";
        foreach($databases as $database){
            $database = trim($database);

            $sql .= "DROP DATABASE $database; ";
        }

        return $sql;
    }

     /**
     * Get the list of the tables
     *
     */
    public function tables()
    {
        $sql = "SHOW TABLES";

        return $sql;
    }
    
     /**
     * has table
     *
     */
    public function hasTable($table)
    {
        $sql = "SHOW TABLES LIKE '$table'";

        return $sql;
    }

    /**
     * Delete Table
     *
     */
    public function deleteTable($tables) {
        // Tables
        if(is_string($tables)){
            $tables = explode(",", $tables);
        }
        
        // Sql
        $sql = "";
        foreach($tables as $table){
            $table = trim($table);

            $sql .= "DROP TABLE $table; ";
        }

        // return
        return $sql;
    }

    /**
     * Rename Table
     *
     */
    public function renameTable($old, $new) {
        // sql
        $sql = "RENAME TABLE `$old` TO `$new`;";

        // return
        return $sql;
    }

    /**
     * Clean Table
     *
     */
    public function cleanTable($table) {
        // sql
        $sql = "TRUNCATE TABLE $table;";

        // return
        return $sql;
    }
   
    /**
     * Create new table
     *
     */
    public function createTable($schema, $table)
    {
        return new CreateTable($schema, $this, $table);
    }

    public function createTableSql($table, $columns)
    {
      // Sql start
      $sql_start = "CREATE TABLE IF NOT EXISTS `$table` (";

      // Sql end
      $sql_end = ")";

      // Sql
      $sql = $this->columnSql($columns);

      $sql = $sql_start . $sql . $sql_end . ';';
      
      return $sql;
    }

    protected function columnSql($columns, $prefix='')
    {
        $sql = '';

        foreach ($columns as $key => $column) {
            // Column name
            $column_name = $key;

            // Key
            $sql .= ' ' . $prefix . "`$key`";
    
            // Type
            $sql .= ' ' . $this->columnsType[$column['type']];
    
            // Parameters
            foreach ($column['parameters'] as $key => $parameter) {
                if(is_array($parameter)){
                    $parameter = "'" . implode("', '", $parameter) . "'" ;
                }
    
                $sql = str_replace(":$key", $parameter, $sql);
            }

            // Attributes
            foreach ($column['attributes'] as $key => $attribute) {
                $sql .= ' ' . $this->attributesType[$attribute];
                
                $parameter = implode("', '", $column['attributes_parameters'][$key]);
                $sql = str_replace(":$key", $parameter, $sql);
                $sql = str_replace(":column", $column_name, $sql);
            }

            // End
            $sql .= ',';
          }

          return rtrim($sql, ',');      
    }
   
    /**
     * Create foreign keys
     *
     */
    public function foreign($schema, $foreign_table, $primary_table)
    {
        return new Foreign($schema, $this, $foreign_table, $primary_table);
    }

    /**
     * Add foreign key
     *
     */
    public function addForeign($foreign_table, $foreign_column, $primary_table, $primary_column, $index=null, $cascade='') {
        $index = (is_null($index)) ? "fk_$foreign_column" : $index;

        $sql = "ALTER TABLE $foreign_table ADD CONSTRAINT $index FOREIGN KEY ($foreign_column) REFERENCES $primary_table($primary_column) $cascade";

        return $sql;
    }

    /**
     * Update foreign key
     *
     */
    public function updateForeign($foreign_table, $foreign_column, $primary_table, $primary_column, $index=null, $cascade='') {
        $index = (is_null($index)) ? "fk_$foreign_column" : $index;
        
        $sql = "
        ALTER TABLE $foreign_table DROP FOREIGN KEY $index;
        ALTER TABLE $foreign_table DROP INDEX $index;
        ALTER TABLE $foreign_table ADD CONSTRAINT $index FOREIGN KEY ($foreign_column) REFERENCES $primary_table($primary_column) $cascade,
        ";

        return $sql;
    }

    /**
     * Delete foreign key
     *
     */
    public function deleteForeign($table, $column, $index=false) {
        $index = ($this->on_update==true) ? "fk_$column" : $index;

        $sql = "
        ALTER TABLE $table DROP FOREIGN KEY $index;
        ALTER TABLE $table DROP INDEX $index;
        ";

        return $sql;
    }

    /**
     * Create column
     *
     */
    public function createColumn($schema, $table) {
        return new CreateColumn($schema, $this, $table);
    }

    public function createColumnSql($table, $columns)
    {
      // Sql start
      $sql_start = "ALTER TABLE $table ";

      // Sql
      $sql = $this->columnSql($columns, 'ADD COLUMN ');
        
      $sql = $sql_start . $sql  . ';';
      
      return $sql;
    }

    /**
     * Get the list of the columns in tables
     *
     */
    public function columns($table)
    {
        $sql = "SHOW COLUMNS FROM `$table`";

        return $sql;
    }

    /**
     * has column
     *
    */
    public function hasColumn($table, $column)
    {
        $sql = "SHOW COLUMNS FROM `$table` LIKE '$column'";

        return $sql;
    }
    
    /**
     * Delete column
     *
     */
    public function deleteColumn($table, $columns) {
        // Columns
        if(is_string($columns)){
            $columns = explode(",", $columns);
        }
        
        $columns = implode(', ', $columns);

        // Sql
        $sql = "ALTER TABLE $table DROP Column $columns";

        // return
        return $sql;
    }

    /**
     * Rename column
     *
     */
    public function renameColumn($table, $old, $new) {
        // sql
        $sql = "ALTER TABLE $table RENAME COLUMN `$old` to `$new`;";

        // return
        return $sql;
    }

}