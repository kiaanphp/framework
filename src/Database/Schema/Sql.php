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
use Kiaan\Database\Schema;

/*
|---------------------------------------------------
| Sql
|---------------------------------------------------
*/
class Sql {
        
    /*
    * SQL
    *
    */
    public function sql($driver, $func, $args=[], $argsIsArray=false)
    {
        if(!is_array($args)){ $args= [$args]; }
        if($argsIsArray){ $args= [$args]; }

        return call_user_func_array([__NAMESPACE__ .'\\'. $driver, $func], $args);
    }

     /*
     * prepare create table
     *
     */
    public static function prepare_create_table($args)
    {
        $types = $args['prepare']["types"];
        $defaults = $args['prepare']["defaults"];
        $defaults_params = $args['prepare']["defaults_params"]; 
        $nulls = $args['prepare']["nulls"]; 
        $attributes = $args['prepare']["attributes"]; 
        $autoincrements = $args['prepare']["autoincrements"]; 
        $uniques = $args['prepare']["uniques"]; 
        $foreigns = $args['prepare']["foreigns"]; 
        $primary_key = $args['prepare']["primary_key"]; 
        $constraint_primary = $args['prepare']["constraint_primary"]; 
        $foreign_key = $args['prepare']["foreign_key"]; 
        $foreign_key_update_action = $args['prepare']["foreign_key_update_action"]; 
        $foreign_key_delete_action = $args['prepare']["foreign_key_delete_action"]; 
        $foreignkeys = $args['prepare']["foreignkeys"];
        $sql_start = $args['prepare']["sql_start"];
        $sql_end = $args['prepare']["sql_end"];

        // table name
        $table = trim($args['table'],' ');

        // columns
        $columns = $args['columns'];

        // primarykey
        $primarykeylist = $args['primarykey'];
        
        // foreignkey
        $foreignkeylist = $args['foreignkey'];


        // columns
        $sql_columns = '';
        foreach ($columns as $key => $column) {
            
            // type
            $type = $types[$column['type']] ?? null;

            // attribute
            $attribute = $attributes[$column['attribute']] ?? null;
            
            // null
            if ($column['null']===true) {
                $column['null'] = "is_null" ;
            }else {
                $column['null'] = "not_null" ;
            }
            $null = $nulls[$column['null']] ?? null;

            // default
            if($column['default']=='default'){
                $default = $defaults_params[$column['default']] ?? null;
                $default = str_replace("?", $column['default_value'], $default);
            }else {
                $default = $defaults[$column['default']] ?? null;
            }

            // autoincrement
            if ($column['autoincrement']===true) {
                $column['autoincrement'] = "auto" ;
            }
            $autoincrement = $autoincrements[$column['autoincrement']] ?? null;

            // unique
            if ($column['unique']===true) {
                $column['unique'] = "unique" ;
            }
            $unique = $uniques[$column['unique']] ?? null; 

            // primarykey
            if ($column['primarykey']===true) {
                $primarykey = $primary_key;
            }else {
                $primarykey = '';
            }
            

            // sql for columns
            $sql_columns .=  "$key $type $attribute $null $default $unique $primarykey $autoincrement,";
            $sql_columns =  trim($sql_columns, ' ');
            $sql_columns = preg_replace('/\s+/', ' ',$sql_columns);
        }

        // foreignkey
        $constraint_foreign_sql = '';
        foreach ($foreignkeylist as $key => $value) {
            $constraint_foreign_sql .= str_replace("?", $key, ', '.$foreign_key);
            $constraint_foreign_sql = str_replace("@@", $value['table'], $constraint_foreign_sql);
            $constraint_foreign_sql = str_replace("$$$", $value['column'], $constraint_foreign_sql);

            // on update
            $foreignkeysaction = $foreignkeys[$value['update']] ?? null;
            if(!empty($foreignkeysaction)){
                $constraint_foreign_sql .= str_replace("?", $foreignkeysaction, $foreign_key_update_action);
            }

            // on delete
            $foreignkeysaction = $foreignkeys[$value['delete']] ?? null;
            if(!empty($foreignkeysaction)){
                $constraint_foreign_sql .= str_replace("?", $foreignkeysaction, $foreign_key_delete_action);
            }

        }

        // sql for constraint primary
        if(sizeof($primarykeylist)>1){
            $primarykeys_str = implode(",", $primarykeylist);
            $constraint_primary_sql = str_replace("?", $primarykeys_str, ', '.$constraint_primary);
        }else{
            $constraint_primary_sql = '';
        }

        // sql for columns
        $sql_columns = trim($sql_columns,',');
        $sql_columns =  trim($sql_columns, ' ');

        // SQL
        $sql = str_replace("?", $table, $sql_start);
        $sql .= $sql_columns;
        $sql .= $constraint_primary_sql;
        $sql .= $constraint_foreign_sql;
        $sql .= $sql_end;

        return $sql;
    }

}