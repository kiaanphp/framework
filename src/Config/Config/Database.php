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

    /**
     * Constructor
     * 
    **/
    public function __construct($pdo, $table) {
        $this->pdo = $pdo;
        $this->table = $table;
    }

    /**
     * Migration
     *
    */
    public function migration()
    {
        // PDO
        $pdo = $this->pdo;

        if ($pdo == NULL) return false;

        // Table
        $table = $this->table;

        // Sql
        $sql = "CREATE TABLE IF NOT EXISTS $table( 
                    id INT AUTO_INCREMENT,
                    title VARCHAR(100) NULL, 
                    value  VARCHAR(100) NOT NULL, 
                    PRIMARY KEY(id)
                )";

        // Execute
        $pdo->exec($sql);
        
        // Return
        return true;
    }

    /**
     * Return all
     *
    */
    public function all() {
        // PDO
        $pdo = $this->pdo;

        // Table
        $table = $this->table;

        // Execute
        $sql = "SELECT * FROM $table";
        $query = $pdo->prepare($sql);
        $query->execute();

        return $query->fetchAll();
    }

    /**
     * Get
     *
    */
    public function get($key, $default='') {
        // PDO
        $pdo = $this->pdo;

        // Table
        $table = $this->table;

        // Execute
        $sql = "SELECT * FROM $table WHERE title='$key' LIMIT 1";
        $row = $pdo->prepare($sql);
        $row->execute();
        $row = $row->fetch();

        if (!$row) {
            $result = $default;
        }else{
            if(empty($row->value) || is_null($row->value)){
               $result = $default;
            }else{
                $result = $row->value;
            }
        }

        return $result;
    }

    /**
     * Set
     *
     */
    public function set($key, $value) {
        // PDO
        $pdo = $this->pdo;

        // Table
        $table = $this->table;

        if($this->has($key)){
            // Update
            $sql = "UPDATE $table SET value='$value' WHERE title='$key'";
        }else{
            // Insert
            $sql = "INSERT INTO $table (title, value) VALUES ('$key', '$value')";
        }

        // Execute
        $row = $pdo->prepare($sql);
        $row->execute();

        return true;
    }

    /**
     * Has
     *
     */
    public function has($key) {
        // PDO
        $pdo = $this->pdo;

        // Table
        $table = $this->table;

        // Execute
        $sql = "SELECT * FROM $table WHERE title='$key' LIMIT 1";
        $row = $pdo->prepare($sql);
        $row->execute();
        $row = $row->fetch();

        if (!$row) {
            return false;
        }else{
            return true;
        }
    }

    /**
     * Delete
     *
     */
    public function delete($key) {
        // PDO
        $pdo = $this->pdo;

        // Table
        $table = $this->table;

        if($this->has($key)){
            $sql = "DELETE FROM $table WHERE title='$key'";
            $row = $pdo->prepare($sql);
            $row->execute();
        }

        return true;
    }
    
    /**
     * Destroy
     *
    */
    public function destroy() {
        // PDO
        $pdo = $this->pdo;

        // Table
        $table = $this->table;

        $sql = "DELETE FROM $table";
        $row = $pdo->prepare($sql);
        $row->execute();

        return true;
    }

    /**
     * Name
     * 
     * Change key name of key
     *
    */
    public function name($key, $value) {
        // PDO
        $pdo = $this->pdo;

        // Table
        $table = $this->table;

        if($this->has($key)){
            $sql = "UPDATE $table SET title='$value' WHERE title='$key'";
            $row = $pdo->prepare($sql);
            $row->execute();
        }

        return true;
    }

}