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
| Columns
|---------------------------------------------------
*/
class Columns {
    
    public $schema;
    public $driver;
    public $table;
    public $column;
    public $columns = array();

    function __construct($schema, $driver, $table) {
        $this->schema = $schema;
        $this->driver = $driver;
        $this->table = $table;
    }

    /*
    * ID
    *
    */
    public function id($name='id'){
        return $this->addType("id", $name);
    }

    /*
    * Big integer
    *
    */
    public function bigInteger($name){
        return $this->addType("bigInteger", $name);
    }

    /*
    * Binary
    *
    */
    public function binary($name){
        return $this->addType("binary", $name);
    }

    /*
    * Boolean
    *
    */
    public function boolean($name){
        return $this->addType("boolean", $name);
    }
    
    /*
    * Char
    *
    */
    public function char($name, $length=30){
        return $this->addType("char", $name, array($length));
    }

    /*
    * Date
    *
    */
    public function date($name){
        return $this->addType("date", $name);
    }

    /*
    * DateTime
    *
    */
    public function dateTime($name){
        return $this->addType("dateTime", $name);
    }

    /*
    * Decimal
    *
    */
    public function decimal($name, $precision=15, $scale=2){
        return $this->addType("decimal", $name, array($precision, $scale));
    }

    /*
    * Double
    *
    */
    public function double($name, $digits=15, $decimal_point=8){
        return $this->addType("double", $name, array($digits, $decimal_point));
    }

    /*
    * Enum
    *
    */
    public function enum($name, array $array){
        return $this->addType("enum", $name, array($array));
    }

    /*
    * float
    *
    */
    public function float($name){
        return $this->addType("float", $name);
    }

    /*
    * Increments
    *
    */
    public function increments($name){
        return $this->addType("increments", $name);
    }
    
    /*
    * Integer
    *
    */
    public function integer($name){
        return $this->addType("integer", $name);
    }  

    /*
    * LongText
    *
    */
    public function longText($name){
        return $this->addType("longText", $name);
    }

    /*
    * MediumInteger
    *
    */
    public function mediumInteger($name){
        return $this->addType("mediumInteger", $name);
    }
    
    /*
    * MediumText
    *
    */
    public function mediumText($name){
        return $this->addType("mediumText", $name);
    } 

    /*
    * SmallInteger
    *
    */
    public function smallInteger($name){
        return $this->addType("smallInteger", $name);
    }

    /*
    * TinyInteger
    *
    */
    public function tinyInteger($name){
        return $this->addType("tinyInteger", $name);
    }

    /*
    * String
    *
    */
    public function string($name, $length=100){
        return $this->addType("string", $name, array($length));
    }

    /*
    * Text
    *
    */
    public function text($name){
        return $this->addType("text", $name);
    }

    /*
    * Time
    *
    */
    public function time($name){
        return $this->addType("time", $name);
    }
    
    /*
    * Json
    *
    */
    public function json($name){
        return $this->addType("json", $name);
    }

    /*
    * Timestamp
    *
    */
    public function timestamp($name){
        return $this->addType("timestamp", $name);
    }

    /*
    * Add type
    *
    */
    protected function addType($type, $name, $parameters=array()){
        $this->columns[$name] = [
            'type' => $type,
            'parameters' => $parameters,
            'attributes' => array(),
            'attributes_parameters' => array()
        ];

        $this->column = $name;

        return clone($this);
    }
    
    /*
    * Auto increment
    *
    */
    public function auto(){
        return $this->addAttribute("auto");
    }

    /*
    * Primary key
    *
    */
    public function primary(){
        return $this->addAttribute("primary");
    }

    /*
    * Unique
    *
    */
    public function unique(){
        return $this->addAttribute("unique");
    }

    /*
    * Positive
    *
    */
    public function positive(){
        return $this->addAttribute("positive");
    }

    /*
    * Update current
    *
    */
    public function updateCurrent(){
        return $this->addAttribute("updateCurrent");
    }

    /*
    * Current
    *
    */
    public function current(){
        return $this->addAttribute("current");
    }

    /*
    * Nullable
    *
    */
    public function nullable(){
        return $this->addAttribute("nullable");
    }

    /*
    * Not nullable
    *
    */
    public function notNullable(){
        return $this->addAttribute("notNullable");
    }

    /*
    * Default
    *
    */
    public function default($value){
        return $this->addAttribute("default", array($value));
    }
    
    /*
    * After
    *
    */
    public function after($column){
        return $this->addAttribute("after", array($column));
    }

    /*
    * Add attribute
    *
    */
    protected function addAttribute($name, array $parameters=array()){
        array_push($this->columns[$this->column]['attributes'], $name);    
        array_push($this->columns[$this->column]['attributes_parameters'], $parameters);    

        return clone($this);
    }

}