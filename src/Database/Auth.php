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
| Auth
|---------------------------------------------------
*/
class Auth {
    
    /*
    * Global traits
    *
    */
    use \Kiaan\Application\Resources\Global\ExtendingTrait;
     
    /*
    * Traits
    *
    */
    use Auth\Helpers;

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
    * Token field
    *
    */
    protected $token_field = "token";

    /*
    * JWT field
    *
    */
    protected $jwt_field = "jwt";

    /*
    * Login Session
    */
    protected $login_session = 'auth';

    /*
    * JWT header
    */
    protected $jwt_header = 'Authorization';
    protected $jwt_header_prefix = 'Bearer';

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
    protected function execute($sql)
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
            "pass_field" => $this->pass_field,
            "token_field" => $this->token_field,
            "jwt_field" => $this->jwt_field
        ];
    }

    /**
     * Set fields
    *
    */
    public function setFields($primary="id", $id="email", $pass="password", $token="token", $jwt="jwt")
    {
        $this->primary_field = $primary;
        $this->id_field = $id;
        $this->pass_field = $pass;
        $this->token_field = $token;
        $this->jwt_field = $jwt;
    }

    /**
     * Set JWT header
    *
    */
    public function setJwtHeader($header, $prefix)
    {
        $this->jwt_header = $header;
        $this->jwt_header_prefix = $prefix;

        return clone($this);
    }

    /**
     * Get JWT header
    *
    */
    public function getJwtHeader()
    {
        return [
            "header" => $this->jwt_header,
            "prefix" => $this->jwt_header_prefix
        ];
    }

    /*
    * Attempt
    *
    */
    public function attempt($id, $primary_field='', $jwt=false)
    {
        $primary_field = (empty($primary_field)) ? $this->primary_field : $primary_field;

        $auth = $this->execute("SELECT * FROM {$this->table} WHERE {$primary_field} = '{$id}' LIMIT 1");
       
        if ($auth) {
            $primary_field = $this->primary_field;

            if(!$jwt){
                $_SESSION[$this->login_session] = $auth->$primary_field;
            }

            return $auth;
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
        $auth = $this->execute("SELECT * FROM {$this->table} WHERE {$this->id_field} = '{$id}' LIMIT 1");

        if($auth){
            // Password Verify
            if($password_is_hash){
                if(password_verify($pass, $auth->$pass_field)) {
                    $_SESSION[$this->login_session] = $auth->$primary_field;

                    return $auth;
                }
            }else{
                if($pass == $auth->$pass_field) {
                    $_SESSION[$this->login_session] = $auth->$primary_field;

                    return $auth;
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

    /**
     * Token
    *
    */
    public function token($id, $pass, $expire=60, $password_is_hash=true)
    {
        // Login
        $login = $this->login($id, $pass, $password_is_hash);

        if(!$login){ return false; }
       
        // ID
        $id = $login->{$this->primary_field};

        // Return
        return $this->generate_jwt_code($id, $expire);
    }

    /**
     * JWT
    *
    */
    public function jwt($jwt=null)
    {
        // JWT
        if(empty($jwt)){
            $header = "HTTP_".strtoupper($this->jwt_header);
            if(isset($_SERVER[$header])){
                $jwt = ltrim($_SERVER[$header], "{$this->jwt_header_prefix}");
                $jwt = trim($jwt, " ");
            }else{
                return false;
            }
        }

        // Check token
        $tokenParts = explode('.', $jwt);

        if(count($tokenParts)==3){
            $payload = json_decode(base64_decode($tokenParts[1]));
        }else{
            return false;
        }

        // Check isset fields
        if(!isset($payload->exp) || !isset($payload->secret)){
            return false;
        }

        // Check the expiration time
        if(!($payload->exp - time()) < 0){
            return false;
        }

        // Check login
        $auth = $this->execute("SELECT * FROM {$this->table} WHERE {$this->jwt_field} = '{$jwt}' and {$this->token_field} = '{$payload->secret}' LIMIT 1");
        if(!$auth){
            return false;
        }

        return $auth;
    }

    /**
     * JWT
    *
    */
    public function reToken($expire=60, $jwt=null)
    {
        // Check token
        $auth = $this->jwt($jwt);
        if(!$auth){
            return false;
        }

        // ID
        $id = $auth->{$this->primary_field};

        // Return
        return $this->generate_jwt_code($id, $expire);
    }

    /**
     * Update token
    *
    */
    public function updateToken($id, $expire=60)
    {
        return $this->generate_jwt_code($id, $expire);
    }

    /*
    * JWT attempt
    *
    */
    public function jwtAttempt($id, $primary_field='')
    {
        return $this->attempt($id, $primary_field, true);
    }

}