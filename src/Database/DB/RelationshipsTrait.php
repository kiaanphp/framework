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
| Relationships trait
|---------------------------------------------------
*/
trait RelationshipsTrait {

    /*
	* Relationships name
	*/
	protected $relationships_name = '';
	
    /*
	* Relationships have name
	*/
	protected $relationships_have_name = '';

    /*
	* Relationships data
	*/
	protected $relationships_data;

	/*
	* Relationships name data
	*/
	protected $relationships_name_data;

	/*
	* Primary key
	*/
	protected $primary_key = 'id';

    /**
    * Have title
	*
    * Custom title for relationships (have)
    */
    public function haveTitle($title)
    {          
        $this->relationships_have_name = $title;

        return clone($this);   
    }

    /**
    * Title
	*
    * Custom title for relationships
    */
    public function title($title)
    {          
        $this->relationships_name = $title;

        return clone($this);   
    }

    /**
    * Clear
	*
    * Clear data
    */
    protected function clear()
    {          
		$this->relationships_name = '';
    }

    /**
    * Key
	*
	* Primary key
    */
    protected function key($primary_key)
    {          
		$this->primary_key = $primary_key;
    }
	
    /**
    * Prepare relationships
	*
	* 
    */
    protected function prepareRelationships($table, $foreign_key, $primary_key, $fetch, $inverse=false, $have=false)
    {    
		// DB
		$db = clone($this);
		$original_table = $db->table;
		$original_relationships_name = $db->relationships_name_data;
		$db->reset();

		// Table
		$table = (class_exists($table)) ? trim((new $table)->table) : trim($table);
		$original_table = ($inverse) ? $table : $original_table;

		// Primary Key
		if (empty($primary_key)) {
			$primary_key = ($inverse) ? $this->singularize($original_table)."_".$this->primary_key : $this->primary_key;
		}else{
			$primary_key = trim($primary_key);
		}

		// Foreign Key
		if (empty($foreign_key)) {
			$foreign_key = ($inverse) ? $this->primary_key : $this->singularize($original_table)."_$primary_key";
		}else{
			$foreign_key = trim($foreign_key);
		}

		// Relationship name
		if (empty($this->relationships_name)) {
			$relationship_name = $table;
		}else{
			$relationship_name = $this->relationships_name;
		}

		if(!$have){
			$this->relationships_name_data = $relationship_name;
		}

		// Data
		$data = json_decode(json_encode($this->data()), true);
		$isIndexed = array_values($data) === $data;
		$data = ($isIndexed) ? $data : array($data) ;

		// Keys
		if($have){
			$original_relationships_data = $this->relationships_data;
			$keys = array_unique(array_column($original_relationships_data, $foreign_key));
			$original_foreign_key = $foreign_key;
			$foreign_key = $primary_key;
		}else{
			$keys = array_column($data, $primary_key);
		}

		// Break if keys is zero
		if(empty($keys)){
			return clone($this);
		}

		// Relationship
		$relationship = $db->table($table)->in($foreign_key, $keys)->get();
		$relationship = json_decode(json_encode($relationship->data()), true);
		$this->relationships_data = $relationship;

		// Attached
		if(!$have){
			foreach ($keys as $index => $key) {
				$data[$index][$relationship_name] = array();

				// Relationship keys
				$relationship_keys = array_keys(array_column($relationship, $foreign_key), $key);
				if($fetch=="first"){
					$relationship_keys = (empty($relationship_keys)) ? array() : array($relationship_keys[0]);
				}
				
				foreach ($relationship_keys as $relationship_key) {
					if($fetch=="first"){
						$data[$index][$relationship_name] = $relationship[$relationship_key];
					}else{
						$data[$index][$relationship_name][] = $relationship[$relationship_key];
					}
				}

			}
	    }else{

			// Relationships name
			$relationship_name = (empty($this->relationships_have_name)) ? $relationship_name : $this->relationships_have_name ;

			foreach ($data as $index => $item) {
				foreach ($item[$original_relationships_name] as $nestedIndex => $nestedItem) {
					$id = $data[$index][$original_relationships_name][$nestedIndex][$original_foreign_key];					

					if(isset(array_flip(array_column($relationship, $primary_key))[$id])){
						$key = array_flip(array_column($relationship, $primary_key))[$id];
					}else{
						$key = null;
					}

					if(isset($relationship[$key])){
						$data[$index][$original_relationships_name][$nestedIndex][$relationship_name] = $relationship[$key];
					}else{
						$data[$index][$original_relationships_name][$nestedIndex][$relationship_name] = array();
					}
					
				}
			}

			$this->relationships_have_name = '';
		}
		
        // Return
	    $this->relationships_name = '';
		
		if(!$isIndexed && count($data)==1){
			$data = $data[0];
		}

        $this->data = json_decode(json_encode($data), false);

        return clone($this);
    }

	/**
    * Has one
	*
	* 
    */
    public function hasOne($table, $foreign_key='', $primary_key='')
    {    
		return $this->prepareRelationships($table, $foreign_key, $primary_key, 'first');
    }

	/**
    * Belong
	*
	* 
    */
    public function belongOne($table, $foreign_key='', $primary_key='')
    {    
		return $this->prepareRelationships($table, $primary_key, $foreign_key, 'first', true);
    }

	/**
    * Has
	*
	* 
    */
    public function has($table, $foreign_key='', $primary_key='')
    {    
		return $this->prepareRelationships($table, $foreign_key, $primary_key, 'get');
    }

	/**
    * Belong
	*
	* 
    */
    public function belong($table, $foreign_key='', $primary_key='')
    {    
		return $this->prepareRelationships($table, $primary_key, $foreign_key, 'get', true);
    }
	
	/**
    * have
	* 
    */
    public function have($table, $linked_table, $table_foreign_key='', $foreign_key='', $table_primary_key='', $primary_key='')
    {    
		// Table
		if(class_exists($table)){
			$table = (new $table)->table;
		}

		// Linked table
		if(class_exists($linked_table)){
			$linked_table = (new $linked_table)->table;
		}

		// Table foreign key
		if(empty($table_foreign_key)){
			$table_foreign_key = $this->singularize($table)."_".$this->primary_key;
		}

		// This foreign key
		if(empty($foreign_key)){
			$foreign_key = $this->singularize($this->table)."_".$this->primary_key;
		}

		// Step 1 : (Linked table)
		$this->prepareRelationships($linked_table, $foreign_key, $primary_key, 'get');

		// Step 2 : (Relationship table)
		return $this->prepareRelationships($table, $table_foreign_key, $table_primary_key, 'get', false, true);
    }

}