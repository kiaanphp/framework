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
namespace Kiaan\Contact;

/*
|---------------------------------------------------
| Notification
|---------------------------------------------------
*/
class Notifi {

    /*
    * Global traits
    *
    */
    use \Kiaan\Application\Resources\Global\ExtendingTrait;

    /* 
    * PDO
    *
    */
    protected $pdo;

    /*
    * Notifications table
    *
    */
    protected $table = "notifications";  

    /*
    * Users table
    *
    */
    protected $users_table = "users";  

    /*
    * Login Session
    *
    */
    protected $login_session = 'auth';

    /*
    * Primary field
    *
    */
    protected $primary_field = "id";

    /*
    * Users list
    *
    */
    protected $users = array();

    /*
    * Select list
    *
    */
    protected $select = array();
    
    /*
    * limit
    *
    */
    protected $limit = null;
        
    /*
    * Offset
    *
    */
    protected $offset = null;
    
    /*
    * Read
    *
    */
    protected $read = null;
    
    /*
    * Data list.
    *
    */
    protected $data = array();
    
    /*
    * Last inserted ID.
    *
    */
    protected $lastId = null;
    
    /**
     * Execute
    *
    */
    protected function execute($sql)
    {
        $db = $this->pdo->prepare($sql);
        $db->execute();
        $db->setFetchMode(\PDO::FETCH_OBJ);
        $fetch = $db->fetchAll();

        $this->lastId = $this->pdo->lastInsertId();

        return $fetch;
    }

    /**
     * Set pdo connection
    *
    */
    public function setPdo($pdo)
    {
        return $this->pdo = $pdo;
    }

    /**
     * Get pdo connection
    *
    */
    public function getPdo()
    {
        return $this->pdo;
    }
    
    /**
     * Get login session
    *
    */
    public function getLoginSession()
    {
        return $this->login_session;
    }

    /**
     * Set login session
    *
    */
    public function setLoginSession($key='auth')
    {
        $this->login_session = $key;
    }

    /**
     * Get table
    *
    */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Set table
    *
    */
    public function setTable($table)
    {
        return $this->table = $table;
    }

    /**
    * Get user table
    *
    */
    public function getUserTable()
    {
        return $this->users_table;
    }

    /**
    * Set user table.
    *
    */
    public function setUserTable($table)
    {
        return $this->users_table = $table;
    }

    /**
    * Get primary field.
    *
    */
    public function getPrimaryField()
    {
        return $this->primary_field;
    }

    /**
    * Set primary field.
    *
    */
    public function setPrimaryField($id)
    {
        return $this->primary_field = $id;
    }
    
    /**
    * Select current user ID.
    *
    */
    public function me()
    {
        if(!isset($_SESSION[$this->login_session])){
            return clone($this);
        }

        return $this->user($_SESSION[$this->login_session]);
    }

    /**
    * User ID.
    *
    */
    public function user($id)
    {
        // ID
        if(is_string($id)){
            $id = explode(",", $id);
        }

        if(is_int($id)){
            $id = array($id);
        } 

        // Remove white space
        $id = array_map('trim', $id);

        // Add to users list
        $this->users = $id;

        // Return
        return clone($this);
    }

    /**
    * Select ID.
    *
    */
    public function select($id)
    {
        // ID
        if(is_string($id)){
            $id = explode(",", $id);
        }

        if(is_int($id)){
            $id = array($id);
        } 

        // Remove white space
        $id = array_map('trim', $id);

        // Add to select list
        $this->select = $id;

        // Return
        return clone($this);
    }

    /**
    * Clean data.
    *
    */
    public function clean($lastId=false)
    {
        $this->users = array();
        $this->select = array();
        $this->limit = null;
        $this->offset = null;
        $this->read = null;
        $this->data = array();
        if($lastId===false){$this->lastId = null;}
    }   
   
    /**
    * List of all read and unread notifications.
    *
    */
    public function readAndUnread()
    {
        $this->read = null;

        return clone($this);
    } 

    public function UnreadAndRead()
    {
        $this->read = null;

        return clone($this);
    } 

    /**
    * List of all read notifications.
    *
    */
    public function read()
    {
        $this->read = true;

        return clone($this);
    } 

    /**
    * List of all unread notifications.
    *
    */
    public function unread()
    {
        $this->read = false;

        return clone($this);
    } 

    /**
    * List of all notifications with limit.
    *
    */
    public function limit(int $count)
    {
        $this->limit = $count;

        return clone($this);
    }

    /**
    * List of all notifications with limit and offset.
    *
    */
    public function offset(int $count)
    {
        $this->offset = $count;

        return clone($this);
    }

    /**
    * Generate query string.
    *
    */
    protected function generateQuery()
    {
        // Query
        $query = "";

        // Where
        $where = array();
        $users = implode(",", $this->users);
        $select = implode(",", $this->select);

        if(!empty($users)){
            $where['users'] = $users;
        }

        if(!empty($select)){
            $where['select'] = $select;
        }
        
        if(!is_null($this->read)){
            $where['read'] = ($this->read===true) ? 'seen=1' : 'seen=0';
        }
        
        
        foreach ($where as $key => $item) {
            if ($key === array_key_first($where)){
                $query .= " WHERE ";
            }else{
                $query .= " AND ";
            }

            switch ($key) {
                case "users":
                    $query .= "user_id IN ({$item})";
                  break;
                case "select":
                    $query .= "id IN ({$item})";
                  break;
                case "read":
                $query .= $item;
                break;
              }            
        }

        // Limit
        if(!is_null($this->limit)){
            $query .= " LIMIT {$this->limit}";
        }

        // Offset
        if(!is_null($this->offset)){
            $query .= " OFFSET {$this->offset}";
        }

        return $query;
    }

    /**
    * List of all notifications.
    *
    */
    public function get()
    {
        // Query
        $query = "SELECT * FROM {$this->table}";

        // Generate query string.
        $query .= $this->generateQuery();

        // Clean data.
        $this->clean();

        // Execute
        return $this->execute($query . ";");
    } 

    /**
    * Mark all selected as read.
    *
    */
    public function asRead(bool $read=true)
    {
        // Read
        $read = ($read===true) ? 1 : 0;

        // Date
        $date = date("Y-m-d h:i:s");

        // Query
        $query = "UPDATE {$this->table} SET seen={$read}, seen_at='{$date}'";

        // Generate query string.
        $query .= $this->generateQuery();

        // Clean data.
        $this->clean();

        // Execute
        $this->execute($query . ";");

        return true;
    }

    /**
    * Mark all selected as un read.
    *
    */
    public function asUnRead()
    {
        return $this->asRead(false);
    }

    /**
    * Send notification.
    *
    */
    public function send(string $data)
    {
        return $this->insert($data);
    }

    /**
    * Send notification.
    *
    */
    public function insert(string $data)
    {
        // Send   
        try {
            foreach ($this->users as $user) {
                $this->execute("INSERT INTO {$this->table} (id, user_id, data) VALUES (NULL, {$user}, '{$data}')");
            }
        } catch (\Throwable $th) {
            // Return false
            return false;
        }    

        // Clean data.
        $this->clean(true);

        // Return true
        return $this->lastId;
    }   

    /**
    * Update data.
    *
    */
    public function update(string $data)
    {
        // Query
        $query = "UPDATE {$this->table} SET data='{$data}'";

        // Generate query string.
        $query .= $this->generateQuery();

        // Clean data.
        $this->clean();

        // Execute
        $this->execute($query . ";");

        return true;
    } 
    
    /**
    * Delete row of notifications.
    *
    */
    public function delete()
    {
        // Query
        $query = "DELETE FROM {$this->table}";

        // Generate query string.
        $query .= $this->generateQuery();
        // Clean data.
        $this->clean();

        // Execute
        $this->execute($query . ";");

        return true;
    }    

}