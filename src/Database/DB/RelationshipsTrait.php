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
namespace Kiaan\Database\DB;

/*
|---------------------------------------------------
| Uses
|---------------------------------------------------
*/
use PDO;

/*
|---------------------------------------------------
| Relationships trait
|---------------------------------------------------
*/
trait RelationshipsTrait {

    /*
	* Relationships name
	*/
	protected $relationships_name = '';

	/*
	* Primary key
	*/
	protected $primary_key = 'id';

    /**
    * Title
    * Custom title for relationships
    */
    public function title($title)
    {          
        $this->relationships_name = $title;

        return $this;   
    }

    /**
    * Clear
    * Clear data
    */
    protected function clear()
    {          
		$this->relationships_name = '';
    }

    /**
    * Key
	* Primary key
    */
    protected function key($primary_key)
    {          
		$this->primary_key = $primary_key;
    }

    /**
     * prepare relationships
    */
    protected function prepareRelationships($excute, $table, $foreign_key, $primary_key)
    {
		// Data
		$data = (array) $this->data;

		// Name relationships
		if (empty($this->relationships_name)) {
			$relationships_name = $table;
		}else{
			$relationships_name = $this->relationships_name;
		}

		// Primary key
		if (empty($primary_key)) {
			$primary_key = $this->primary_key;
		}

		// Foreign key
		if (empty($foreign_key)) {
			$foreign_key = $this->primary_key;
		}

		// Primary key values
		$primary_key_values = (array_keys($data) === range(0, count($data) - 1)) ? array_column($data, $primary_key) : array($data[$primary_key]) ?? null;
		
		// Loop
		foreach($primary_key_values as $key => $primary_key_value){
			$result = $this->db->table($table)->where($foreign_key, $primary_key_value)->{$excute}();

			$result_data = $result->data;

			if(is_object($this->data)){
				$this->data->{$relationships_name} = $result_data;
			}else{
				$this->data[$key]->{$relationships_name} = $result_data;
			}
		}

		// Clear data
		$this->clear();

		// Result
		return $this->data;
	}
	
	/*
	* (And) prepare relationships for models
	*
	*/
	protected function _prepareRelationships($excute, $type, $class, $foreign_key='', $primary_key='')
    {
		// Get class
		$class = new $class;
	
		// Get table
		$table = $class->table;

		if($type=='and'){ // And
			// Get primary key
			if(empty($primary_key)){
				$primary_key = $this->db->foreign_keys[$class->table];
			}

			// Get foreign key
			if(empty($foreign_key)){
				$foreign_key = $this->db->primary_keys[$class->table] ?? $this->db->primary_key;
			}
		}else{ // With
			// Get primary key
			if(empty($primary_key)){
				$primary_key = $class->primary_keys[$this->db->table] ?? $class->primary_key;
			}

			// Get foreign key
			if(empty($foreign_key)){
				$foreign_key = $class->foreign_keys[$this->db->table];
			}
		}
		
		// Excute
		return $this->prepareRelationships($excute, $table, $primary_key, $foreign_key);
	}

	/*
	* And
	*
	*/
	public function and($table, $foreign_key='', $primary_key='')
    {
		// Model
		if(class_exists($table)){
			return $this->_prepareRelationships("first", "and", $table, $primary_key, $foreign_key);
		}

		// Excute
		return $this->prepareRelationships("first", $table, $foreign_key, $primary_key);
    }

	/*
	* And all
	*
	*/
	public function andAll($table, $foreign_key='', $primary_key='')
    {		
		// Model
		if(class_exists($table)){
			return $this->_prepareRelationships("get", "and", $table, $primary_key, $foreign_key);
		}

		// Excute
		return $this->prepareRelationships("get", $table, $foreign_key, $primary_key);
    }

	/*
	* With
	*
	*/
	public function with($table, $primary_key='', $foreign_key='')
    {		
		// Model
		if(class_exists($table)){
			return $this->_prepareRelationships("first", "with", $table, $primary_key, $foreign_key);
		}

		// Excute
		return $this->prepareRelationships("first", $table, $foreign_key, $primary_key);
    }

	/*
	* With all
	*
	*/
	public function withAll($table, $primary_key='', $foreign_key='')
    {		
		// Model
		if(class_exists($table)){
			return $this->_prepareRelationships("get", "with", $table, $primary_key, $foreign_key);
		}

		// Excute
		return $this->prepareRelationships("get", $table, $foreign_key, $primary_key);
    }

}