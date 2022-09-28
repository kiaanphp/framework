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
namespace Kiaan\Sessions;

/*
|---------------------------------------------------
| Session
|---------------------------------------------------
*/
class Session {
    
    /*
    * Global traits
    *
    */
    use \Kiaan\Application\Resources\Global\ExtendingTrait;
     
    /**
     * Set new session
     *
     * @param string $key
     * @param string $value
     *
     * @return string $value
     */
    public function set($key, $value) {
        $_SESSION[$key] = $value;

        return $value;
    }

    /**
     * Check that session has the key
     *
     * @param string $key
     *
     * @return bool
     */
    public function has($key) {
        return isset($_SESSION[$key]);
    }

    /**
     * Get session by the given key
     *
     * @param string $key
     *
     * @return mixed
     */
    public function get($key, $default=null) {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    }

    /**
     * Delete session by the given key
     *
     * @param string $key
     * @return void
     */
    public function delete($key) {
        unset($_SESSION[$key]);

        return $_SESSION;
    }

    /**
     * Destroy the session
     *
     * return void
     */
    public function destroy() {
        return $_SESSION = [];
    }

    /**
     * Change key name of session
     *
     */
    public function key($key, $name) {
        if (array_key_exists($key, $_SESSION)) {
            $_SESSION[$name] = $_SESSION[$key];
            unset($_SESSION[$key]);
        }
        
        return $_SESSION[$name] ?? false;
    }

    /**
     * Flash session
     *
     * @params string $key
     * @return string $value
     */
    public function flash($key) {
        if (isset($_SESSION[$key])) {
            $value = $_SESSION[$key];

            unset($_SESSION[$key]);

            return $value;
        }
        
        return false;
    }

    /**
     * Return array for all sessions
    * 
    */
    public function toArray() {
        return $_SESSION;
    }
  
    /**
     * Return object for all sessions
     * 
    */
    public function toObject() {
        return json_decode(json_encode($this->toArray()), false);
    }

    /**
     * Return object for all sessions
     * 
     */
    public function all() {
        return $this->toObject();
    }    
}