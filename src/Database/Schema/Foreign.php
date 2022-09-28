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
| Foreign
|---------------------------------------------------
*/
class Foreign {
    
    public $schema;
    public $driver;
    public $foreign_table;
    public $primary_table;
    public $on_update = false;
    public $on_delete = false;

    function __construct($schema, $driver, $foreign_table, $primary_table) {
        $this->schema = $schema;
        $this->driver = $driver;
        $this->foreign_table = $foreign_table;
        $this->primary_table = $primary_table;
    }

    /*
    * ON UPDATE CASCADE
    *
    */
    public function onUpdate(){
        $this->on_update = true;

        return clone($this);
    }

    /*
    * ON DELETE  CASCADE
    *
    */
    public function onDelete(){
        $this->on_delete = true;

        return clone($this);
    }

    /*
    * Add foreign key
    *
    */
    public function add($foreign_column, $primary_column, $index=null){
        // Cascade
        $on_update = ($this->on_update==true) ? "ON UPDATE CASCADE" : '';
        $on_delete = ($this->on_delete==true) ? " ON DELETE CASCADE" : '';
        $cascade = $on_update . $on_delete;

        $sql = ($this->driver)->addForeign($this->foreign_table, $foreign_column, $this->primary_table, $primary_column, $index, $cascade);

        $this->schema->execute($sql);

        return true;
    }

    /*
    * Update foreign key
    *
    */
    public function update($foreign_column, $primary_column, $index=null){
        // Cascade
        $on_update = ($this->on_update==true) ? "ON UPDATE CASCADE" : '';
        $on_delete = ($this->on_delete==true) ? " ON DELETE CASCADE" : '';
        $cascade = $on_update . $on_delete;

        $sql = ($this->driver)->updateForeign($this->foreign_table, $foreign_column, $this->primary_table, $primary_column, $index, $cascade);

        $this->schema->execute($sql);

        return true;
    }

    /*
    * Delete foreign key
    *
    */
    public function delete($column, $index=false){
        $sql = ($this->driver)->deleteForeign($this->foreign_table, $column, $index);

        $this->schema->execute($sql);

        return true;
    }

}