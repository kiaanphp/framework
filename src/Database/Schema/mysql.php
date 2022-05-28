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
use Kiaan\Database\Schema\Sql;

/*
|---------------------------------------------------
| mysql
|---------------------------------------------------
*/
class mysql {

     /**
     * Get the name of the currently used database
     *
     */
    public static function database()
    {
        // sql
        $sql = 'SELECT database()';
        $parms = [];

        return ["sql" => $sql, "parms" => $parms];
    }

     /**
     * Get the name of databases
     *
     */
    public static function databases()
    {        
        $sql = "SHOW DATABASES";
        $parms = [];

        return ["sql" => $sql, "parms" => $parms];
    }

    /**
     * Create new databases
     *
     */
    public static function createDatabase($database)
    {
        $sql = "CREATE DATABASE $database";
        $parms = [];

        return ["sql" => $sql, "parms" => $parms];
    }

    /**
     * Delete databases
     *
     */
    public static function deleteDatabase($database)
    {
        $sql = "DROP DATABASE $database";
        $parms = [];

        return ["sql" => $sql, "parms" => $parms];
    }

     /**
     * Get the list of the tables
     *
     */
    public static function tables()
    {
        $sql = "SHOW TABLES";
        $parms = [];

        return ["sql" => $sql, "parms" => $parms];
    }
    
     /**
     * has table
     *
     */
    public static function hasTable($table)
    {
        $sql = "SHOW TABLES LIKE '$table'";
        $parms = [];

        return ["sql" => $sql, "parms" => $parms];
    }

    /**
     * Create new table
     *
     */
    public static function createTable($args)
    {
      // List of types
      $types = [
       "key" => "INT(255)",
       "integer" => "INT(255)",
       "float" => "DOUBLE",
       "blob" => "LONGBLOB",
       "date" => "DATE",
       "datetime" => "DATETIME",
       "time" => "TIME",
       "timestamp" => "TIMESTAMP",
       "string" => "VARCHAR(255)",
       "text" => "LONGTEXT",
       "boolean" => "BOOLEAN",
       "number" => "DOUBLE"
      ];

      // List of default
      $defaults = [
       "default" => "DEFAULT",
       "null" => "DEFAULT NULL",
       "current" => "DEFAULT CURRENT_TIMESTAMP",
      ];

      // List of default (params)
      $defaults_params = [
       "default" => "DEFAULT ?",
      ];

      // nulls
      $nulls = [
       "is_null" => "NULL",
       "not_null" => "NOT NULL",
      ];

      // attributes
      $attributes = [
       "positive" => "unsigned",
       "update_current" => "ON UPDATE CURRENT_TIMESTAMP",
      ]; 

      // autoincrements
      $autoincrements = [
       "auto" => "AUTO_INCREMENT",
      ];     

      // uniques
      $uniques = [
       "unique" => "UNIQUE",
      ];    

      // foreigns
      $foreigns = [
       "cascade" => "CASCADE",
       "null" => "SET NULL",
       "restrict" => "RESTRICT",
       "no" => "NO ACTION",
      ];  
      
      // primary key
      $primary_key = "PRIMARY KEY";

      // constraint primary
      $constraint_primary = "CONSTRAINT primarykey PRIMARY KEY (?)";

      // foreign key
      $foreign_key = "FOREIGN KEY (?) REFERENCES @@ ($$$)";
      $foreign_key_update_action = " ON UPDATE ?";
      $foreign_key_delete_action = " ON DELETE ?";

      $foreignkeys = [
       "cascade" => "CASCADE",
       "null" => "SET NULL",
       "restrict" => "RESTRICT",
       "no" => "NO ACTION",
       ];

      // sql start
      $sql_start = "CREATE TABLE IF NOT EXISTS `?` (";

      // sql end
      $sql_end = ")";

       // prepare //
       $args['prepare'] = [
           "types" => $types,
           "defaults" => $defaults,
           "defaults_params" => $defaults_params,
           "nulls" => $nulls,
           "attributes" => $attributes,
           "autoincrements" => $autoincrements,
           "uniques" => $uniques,
           "foreigns" => $foreigns,
           "primary_key" => $primary_key,
           "constraint_primary" => $constraint_primary,
           "foreign_key" => $foreign_key,
           "foreign_key_update_action" => $foreign_key_update_action,
           "foreign_key_delete_action" => $foreign_key_delete_action,
           "foreignkeys" => $foreignkeys,
           "sql_start" => $sql_start,
           "sql_end" => $sql_end
          ];

       $sql = Sql::prepare_create_table($args);
       
       // params        
       $parms = [];

       // return
       return ["sql" => $sql, "parms" => $parms];
    }

    /**
     * Delete Table
     *
     */
    public static function deleteTable($args) {
        // table
        $table = $args;

        // sql
        $sql = "DROP TABLE $table;";

        // params        
        $parms = [];

        // return
        return ["sql" => $sql, "parms" => $parms];
    }

    /**
     * Rename Table
     *
     */
    public static function renameTable($old, $new) {
        // sql
        $sql = "RENAME TABLE `$old` TO `$new`;";

        // params        
        $parms = [];

        // return
        return ["sql" => $sql, "parms" => $parms];
    }

    /**
     * Clean Table
     *
     */
    public static function CleanTable($args) {
        // table
        $table = $args;

        // sql
        $sql = "TRUNCATE TABLE $table;";

        // params        
        $parms = [];

        // return
        return ["sql" => $sql, "parms" => $parms];
    }

     /**
     * Get the list of the columns in tables
     *
     */
    public static function columns($table)
    {
        $sql = "SHOW COLUMNS FROM `$table`";
        $parms = [];

        return ["sql" => $sql, "parms" => $parms];
    }

    /**
     * has column
     *
    */
    public static function hasColumn($args)
    {
        $table = $args["table"];
        $column = $args["column"];

        $sql = "SHOW COLUMNS FROM `$table` LIKE '$column'";
        $parms = [];

        return ["sql" => $sql, "parms" => $parms];
    }
    
    /**
     * Delete column
     *
     */
    public static function deleteColumn($table, $columns) {

        // Columns
        $columns_string = '';
        foreach ($columns as $key => $value) {
           $columns_string .= " DROP `$value`";
           if(sizeof($columns) != $key+1){$columns_string .= ',';}
        }

        // sql
        $sql = "ALTER TABLE `$table` $columns_string;";
        
        // params        
        $parms = [];

        // return
        return ["sql" => $sql, "parms" => $parms];
    }

}