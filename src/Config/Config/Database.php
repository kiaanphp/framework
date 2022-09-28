<?php

/**
 * Kiaan framework
 *
 * @author Hassan Kerdash kerdashhassan@gmail.com
 **/

/*
|---------------------------------------------------
| Namespace
|---------------------------------------------------
*/
namespace Kiaan\Config\Config;

/*
|---------------------------------------------------
| database
|---------------------------------------------------
*/
class Database {

    /*
    * Key field
    *
    */
    protected $key_field = "title";

    /*
    * Value field
    *
    */
    protected $value_field = "value";

    /*
    * Config
    *
    */
    protected $config;

    /**
     * Constructor
     * 
    **/
    public function __construct($config, $pdo, $table) {
        $this->config = $config;
        $this->pdo = $pdo;
        $this->table = $table;

        if(empty($config->db_cache)){
            $this->load();
        }
    }

    /**
     * Load all rows from database
     * 
    */
    public function load() {
        // Execute
        $sql = "SELECT * FROM {$this->table}";
        $query = $this->pdo->prepare($sql);
        $query->execute();

        return $this->config->db_cache = $query->fetchAll();
    }

    /**
     * Get
     *
    */
    public function get($key, $default='') {
        // Convert data to array
        $data = $this->convertToArray();

        // Get a index
        $index = $this->getIndex($data, $key);

        // Default
        if(!is_numeric($index)){
            return $default;
        }

       return $data[$index][$this->value_field];
    }

    /**
     * Set
     *
     */
    public function set($key, $value) {
        // Convert data to array
        $data = $this->convertToArray();

        // Get a index
        $index = array_search($key, array_column($data, $this->key_field));

        if(!is_numeric($index)){
            // Insert
            $sql = "INSERT INTO {$this->table} ({$this->key_field}, {$this->value_field}) VALUES ('$key', '$value')";
        }else{
            // Update
            $sql = "UPDATE {$this->table} SET {$this->value_field}='$value' WHERE {$this->key_field}='$key'";
        }

        // Execute
        $this->pdo->prepare($sql)->execute();

        // Save to cache
        $this->saveTocache($data);
        
        return clone($this);
    }

    /**
     * Has
     *
    */
    public function has($key) {
        // Convert data to array
        $data = $this->convertToArray();

        // Get a index
        $index = $this->getIndex($data, $key);

        if(!is_numeric($index)){
            return false;
        }

        return true;
    }

    /**
     * Delete
     *
    */
    public function delete($key) {
        // Convert data to array
        $data = $this->convertToArray();

        if(is_array($key)){
            // Keys for remove
            $indexs = array();
            foreach ($key as $element) {
                $index = $this->getIndex($data, $key);

                if(is_numeric($index)){
                    $indexs[] = $index;
                }
            }
    
            // Remove from data
            $data = array_diff_key($data, array_flip($indexs)); 

            // Keys implode
            $key = "'".implode("', '", $key)."'";
        }else{
            // Remove from data
            $index = $this->getIndex($data, $key);
            
            if(is_numeric($index)){
                unset($data[$index]);
            }
            
            // Keys implode
            $key = "'".$key."'";
        }

        // Delete from database
        $sql = "DELETE FROM {$this->table} WHERE {$this->key_field} IN ($key)";
        $this->pdo->prepare($sql)->execute();

        // Save to cache
        $this->saveTocache($data);
        
        return clone($this);
    }
    
    /**
     * Destroy
     *
    */
    public function destroy() {
        // Execute
        $sql = "DELETE FROM {$this->table}";
        $this->pdo->prepare($sql)->execute();

        return $this->config->db_cache = array();
    }

    /**
     * Change key name of key
     *
    */
    public function key($key, $value) {
        // Convert data to array
        $data = $this->convertToArray();

        // Get a index
        $index = $this->getIndex($data, $key);

        // Update
        if(is_numeric($index) && !$this->has($value)){
            $data[$index]['title'] = $value;

            // Database
            $sql = "UPDATE {$this->table} SET {$this->key_field}='$value' WHERE {$this->key_field}='$key'";
            $this->pdo->prepare($sql)->execute();
        }
        
        // Save to cache
        $this->saveTocache($data);
        
        return clone($this);
    }

   /*
    * Get index for key
    *
    */
    protected function getIndex($data, $key) {
        return array_search(trim($key), array_column($data, $this->key_field));
    }

    /*
    * Convert data to array
    *
    */
    protected function convertToArray() {
        return json_decode(json_encode($this->config->db_cache), true);
    }
    
    /*
    * Save data to cache
    *
    */
    protected function saveTocache($data) {
        return $this->config->db_cache = json_decode(json_encode($data), false);
    }

    /**
     * Return array for all data
    * 
    */
    public function toArray() {
        return $this->config->db_cache;
    }
  
    /**
     * Return object for all data
     * 
    */
    public function toObject() {
        return json_decode(json_encode($this->toArray()), false);
    }

    /**
     * Return object for all data
     * 
     */
    public function all() {
        return $this->toObject();
    }

}