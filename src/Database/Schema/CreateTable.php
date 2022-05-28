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
| Create table
|---------------------------------------------------
*/
class CreateTable {
    
    public static $DbClass;
    public static $sqlClass;
    public static $sqlDriver;

    protected static $table;
    protected static $primarykey=[];
    protected static $foreignkey=[];
    protected static $columns=[];
    protected static $last_column;

    /**
     * init
     */
    public static function init($table) {
        static::$table = $table;
        return new static;
    }

    // number
    public static function number($column){
        $array = [$column => [
                "type" => "number", //string
               "attribute" => "", //positive or update_current 
               "null" => false, //false or true
               "default"=> "", //value, 
               "default_value" => "", //string
               "autoincrement" => false, //false or true
               "primarykey" => false, //false or true
               "foreignkey" => false, //false or true
               "unique" => false, //false or true
                ]];
        static::$columns = array_merge(static::$columns, $array);

        static::$last_column = $column;
        
        return new static;
    }

    // boolean
    public static function boolean($column){
        $array = [$column => [
               "type" => "boolean", //string
               "attribute" => "", //positive or update_current 
               "null" => false, //false or true
               "default"=> "", //value, 
               "default_value" => "", //string
               "autoincrement" => false, //false or true
               "primarykey" => false, //false or true
               "foreignkey" => false, //false or true
               "unique" => false, //false or true
                ]];
        static::$columns = array_merge(static::$columns, $array);

        static::$last_column = $column;
        
        return new static;
    }

    // text
    public static function text($column){
        $array = [$column => [
                "type" => "text", //string
               "attribute" => "", //positive or update_current 
               "null" => false, //false or true
               "default"=> "", //value, 
               "default_value" => "", //string
               "autoincrement" => false, //false or true
               "primarykey" => false, //false or true
               "foreignkey" => false, //false or true
               "unique" => false, //false or true
                ]];
        static::$columns = array_merge(static::$columns, $array);

        static::$last_column = $column;
        
        return new static;
    }

    // string
    public static function string($column){
        $array = [$column => [
                "type" => "string", //string
               "attribute" => "", //positive or update_current 
               "null" => false, //false or true
               "default"=> "", //value, 
               "default_value" => "", //string
               "autoincrement" => false, //false or true
               "primarykey" => false, //false or true
               "foreignkey" => false, //false or true
               "unique" => false, //false or true
                ]];
        static::$columns = array_merge(static::$columns, $array);

        static::$last_column = $column;
        
        return new static;
    }

    // timestamp
    public static function timestamp($column){
        $array = [$column => [
                "type" => "timestamp", //string
               "attribute" => "", //positive or update_current 
               "null" => false, //false or true
               "default"=> "", //value, 
               "default_value" => "", //string
               "autoincrement" => false, //false or true
               "primarykey" => false, //false or true
               "foreignkey" => false, //false or true
               "unique" => false, //false or true
                ]];
        static::$columns = array_merge(static::$columns, $array);

        static::$last_column = $column;
        
        return new static;
    }

    // time
    public static function time($column){
        $array = [$column => [
                "type" => "time", //string
               "attribute" => "", //positive or update_current 
               "null" => false, //false or true
               "default"=> "", //value, 
               "default_value" => "", //string
               "autoincrement" => false, //false or true
               "primarykey" => false, //false or true
               "foreignkey" => false, //false or true
               "unique" => false, //false or true
                ]];
        static::$columns = array_merge(static::$columns, $array);

        static::$last_column = $column;
        
        return new static;
    }

    // datetime
    public static function datetime($column){
        $array = [$column => [
                "type" => "datetime", //string
               "attribute" => "", //positive or update_current 
               "null" => false, //false or true
               "default"=> "", //value, 
               "default_value" => "", //string
               "autoincrement" => false, //false or true
               "primarykey" => false, //false or true
               "foreignkey" => false, //false or true
               "unique" => false, //false or true
                ]];
        static::$columns = array_merge(static::$columns, $array);

        static::$last_column = $column;
        
        return new static;
    }

    // date
    public static function date($column){
        $array = [$column => [
               "type" => "date", //string
               "attribute" => "", //positive or update_current 
               "null" => false, //false or true
               "default"=> "", //value, 
               "default_value" => "", //string
               "autoincrement" => false, //false or true
               "primarykey" => false, //false or true
               "foreignkey" => false, //false or true
               "unique" => false, //false or true
                ]];
        static::$columns = array_merge(static::$columns, $array);

        static::$last_column = $column;
        
        return new static;
    }

    // blob
    public static function blob($column){
        $array = [$column => [
               "type" => "blob", //string
               "attribute" => "", //positive or update_current 
               "null" => false, //false or true
               "default"=> "", //value, 
               "default_value" => "", //string
               "autoincrement" => false, //false or true
               "primarykey" => false, //false or true
               "foreignkey" => false, //false or true
               "unique" => false, //false or true
                ]];
        static::$columns = array_merge(static::$columns, $array);

        static::$last_column = $column;
        
        return new static;
    }

    // float
    public static function float($column){
        $array = [$column => [
               "type" => "float", //string
               "attribute" => "", //positive or update_current 
               "null" => false, //false or true
               "default"=> "", //value, 
               "default_value" => "", //string
               "autoincrement" => false, //false or true
               "primarykey" => false, //false or true
               "foreignkey" => false, //false or true
               "unique" => false, //false or true
               ]];
       static::$columns = array_merge(static::$columns, $array);

       static::$last_column = $column;
       
       return new static;
   }

    // key
    public static function key($column){
        $array = [$column => [
               "type" => "key", //string
              "attribute" => "", //positive or update_current 
              "null" => false, //false or true
              "default"=> "", //value, 
              "default_value" => "", //string
              "autoincrement" => false, //false or true
              "primarykey" => false, //false or true
              "foreignkey" => false, //false or true
              "unique" => false, //false or true
               ]];
       static::$columns = array_merge(static::$columns, $array);

       static::$last_column = $column;

       return new static;
    }

    // integer
    public static function integer($column){
         $array = [$column => [
                "type" => "integer", //string
               "attribute" => "", //positive or update_current 
               "null" => false, //false or true
               "default"=> "", //value, 
               "default_value" => "", //string
               "autoincrement" => false, //false or true
               "primarykey" => false, //false or true
               "foreignkey" => false, //false or true
               "unique" => false, //false or true
                ]];
        static::$columns = array_merge(static::$columns, $array);

        static::$last_column = $column;
        
        return new static;
    }

    // Primary key
    public static function primary(){
        array_push(static::$primarykey, static::$last_column);

        return new static;
    }

    // Foreign key
    public static function foreign($table, $column, $update='', $delete=''){
        $array = [static::$last_column => [
            "table" => trim($table," "),
            "column" => trim($column," "),
            "update" => trim($update," "),
            "delete" => trim($delete," ")
            ]];
            
        static::$foreignkey = array_merge(static::$foreignkey, $array);

        return new static;
    }

    // autoincrement
    public static function auto(){
        static::$columns[static::$last_column]['autoincrement'] = true;

        return new static;
    }

    // unique
    public static function unique(){
        static::$columns[static::$last_column]['unique'] = true;

        return new static;
    }

    // Update current
    public static function updateCurrent(){
        static::$columns[static::$last_column]['attribute'] = 'update_current';

        return new static;
    }

    // positive
    public static function positive(){
        static::$columns[static::$last_column]['attribute'] = 'positive';

        return new static;
    }
    
    // current
    public static function current(){
        static::$columns[static::$last_column]['default'] = 'current';

        return new static;
    }

    // null
    public static function null(){
    static::$columns[static::$last_column]['default'] = 'null';

    return new static;
    }

    // default
    public static function default($value){
    static::$columns[static::$last_column]['default'] = 'default';
    static::$columns[static::$last_column]['default_value'] = $value;

    return new static;
    }

    // is null
    public static function isNull(){
        static::$columns[static::$last_column]['null'] = true;

        return new static;
    }

    // not null
    public static function notNull(){
        static::$columns[static::$last_column]['null'] = false;

        return new static;
    }

    public static function submit(){
        // primary key
        if(sizeof(static::$primarykey)==1){
            static::$columns[static::$primarykey[0]]['primarykey'] = true;
            static::$primarykey = [];
        }

        // rgs
        $args = ["table" => static::$table, "columns" => static::$columns, "primarykey" => static::$primarykey, "foreignkey" => static::$foreignkey];

        // sql
        $sql = (self::$sqlClass)->sql(self::$sqlDriver, 'createTable', $args, true);

        // execute
        $execute = self::$DbClass->execute($sql['sql'], $sql['parms'], 'assoc');

        // Clean
        static::$table='';
        static::$primarykey=[];
        static::$foreignkey=[];
        static::$columns=[];
        static::$last_column='';
        
        // return
        return true;
    }
    
}
