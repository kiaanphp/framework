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
namespace Kiaan\Database;

/*
|---------------------------------------------------
| Uses
|---------------------------------------------------
*/
use PDO;

/*
|---------------------------------------------------
| Auth
|---------------------------------------------------
*/
class Auth {

    /* 
    * PDO
    *
    */
    protected $pdo;

    /* 
    * Table
    *
    */
    protected $table;

    /*
    * Primary field
    *
    */
    protected $primary_field = "id";

    /*
    * ID field
    *
    */
    protected $id_field = "email";

    /*
    * Password field
    *
    */
    protected $pass_field = "password";

    /*
    * Login Session
    */
    protected $login_session = 'auth';

    /**
     * Constructor
     *
     */
    public function __construct($pdo) {
        // PDO class
        $this->pdo = $pdo;
    }

    /**
     * Set connect
    *
    */
    public function setConnect($pdo)
    {
        return $this->pdo = $pdo;
    }

    /**
     * Get connect
    *
    */
    public function getConnect()
    {
        return $this->pdo;
    }

    /**
     * Execute
    *
    */
    public function execute($sql)
    {
        $db = $this->pdo->prepare($sql);
        $db->execute();
        $db->setFetchMode(\PDO::FETCH_OBJ);
        return $db->fetchAll()[0] ?? $db->fetchAll();
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
     * Get fields
    *
    */
    public function getFields()
    {
        return [
            "primary_field" => $this->primary_field,
            "id_field" => $this->id_field,
            "pass_field" => $this->pass_field
        ];
    }

    /**
     * Set fields
    *
    */
    public function setFields($primary="id", $id="email", $pass="password")
    {
        $this->primary_field = $primary;
        $this->id_field = $id;
        $this->pass_field = $pass;
    }
    
    /*
    * Attempt
    *
    */
    public function attempt($id)
    {
        $auth = $this->execute("SELECT * FROM {$this->table} WHERE {$this->primary_field} = '{$id}' LIMIT 1");
       
        if ($auth) {
            $primary_field = $this->primary_field;

            $_SESSION[$this->login_session] = $auth->$primary_field;

            return true;
        }

        return false;
    }

    /*
    * Login
    *
    */
    public function login($id, $pass, $password_is_hash=true)
    {
        // Fields
        $pass_field = $this->pass_field;
        $primary_field = $this->primary_field;

        // Get auth
        $auth = $this->execute("SELECT * FROM `{$this->table}` WHERE {$this->id_field} = '{$id}' LIMIT 1");

        if($auth){
            // Password Verify
            if($password_is_hash){
                if(password_verify($pass, $auth->$pass_field)) {
                    $_SESSION[$this->login_session] = $auth->$primary_field;

                    return true;
                }
            }else{
                if($pass == $auth->$pass_field) {
                    $_SESSION[$this->login_session] = $auth->$primary_field;

                    return true; 
                }
            }
        }

        return false;
    }

    /*
    * Logout
    *
    */
    public function logout() {
        unset($_SESSION[$this->login_session]);
    }

    /*
    * Check
    *
    */
    public function check() {
        if(isset(($_SESSION[$this->login_session]))){
            return true;
        }

        return false;
    }

    /*
    * User
    *
    */
    public function user() {
        if(isset(($_SESSION[$this->login_session]))){
            $key = $_SESSION[$this->login_session];
            $user = $this->execute("SELECT * FROM {$this->table} WHERE {$this->primary_field} = '{$key}' LIMIT 1");
            unset($user->{$this->pass_field});
            return $user;
        }
        
        return false;
    }

    /**
     * Password hash
    *
    */
    public function hash($pass)
    {
        return password_hash($pass, PASSWORD_DEFAULT);
    }

    /**
     * Password verify
    *
    */
    public function verify($pass, $hash)
    {
        return password_verify($pass, $hash);
    }

}