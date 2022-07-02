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
     * Return all sessions
     *
     * @return array
     */
    public function all() {
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
    public function name($key, $name) {
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
    
}