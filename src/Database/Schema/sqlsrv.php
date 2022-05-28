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

/*
|---------------------------------------------------
| sqlsrv
|---------------------------------------------------
*/
class sqlsrv {

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
        $sql = "SELECT name FROM master.dbo.sysdatabases";
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
        $sql = "select table_name from information_schema.tables";
        $parms = [];

        return ["sql" => $sql, "parms" => $parms];
    }
    
     /**
     * has table
     *
     */
    public static function hasTable($table)
    {
        $sql = "select TABLE_NAME from information_schema.tables WHERE table_name='$table'";
        $parms = [];

        return ["sql" => $sql, "parms" => $parms];
    }

    /**
     * Create Table
     *
     */
    public static function create_table($args)
    {

        // List of types
      $types = [
       "key" => "INTEGER",
       "integer" => "INTEGER",
       "float" => "float",
       "blob" => "VARBINARY(max)",
       "date" => "date",
       "datetime" => "datetime",
       "time" => "time",
       "timestamp" => "time",
       "string" => "NVARCHAR(255)",
       "text" => "NVARCHAR(max)",
       "boolean" => "BIT",
       "number" => "float"
      ];

      // List of default
      $defaults = [
       "default" => "DEFAULT",
       "null" => "DEFAULT NULL",
       "current" => "DEFAULT getdate()",
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
       "update_current" => "",
      ]; 

      // autoincrements
      $autoincrements = [
       "auto" => "IDENTITY",
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
      $sql_start = "CREATE TABLE [?] (";

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
        $sql = "select * from information_schema.columns where table_name = '$table'";
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

        $sql = "SELECT $column FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '$table' ORDER BY ORDINAL_POSITION";
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