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
namespace Kiaan\Cookies;

/*
|---------------------------------------------------
| Base
|---------------------------------------------------
*/
class Cookie {

    /**
     * Set new cookie
     *
     * @param string $key
     * @param string $value
     *
     */
    public function set($key, $value) {
        $expired = time() + (1 * 365 * 24 * 60 * 60);
        setcookie($key, $value, $expired, '/', '', false, true);

        return $value;
    }

    /**
     * Check that cookie has the key
     *
     * @param string $key
     *
     */
    public function has($key) {
        return isset($_COOKIE[$key]);
    }

    /**
     * Get cookie by the given key
     *
     * @param string $key
     *
     */
    public function get($key, $default=null) {
        return isset($_COOKIE[$key]) ? $_COOKIE[$key] : $default;
    }

    /**
     * Delete cookie by the given key
     *
     * @param string $key
     */
    public function delete($key) {
        unset($_COOKIE[$key]);
        setcookie($key, null, '-1', '/');

        return $_COOKIE;
    }

    /**
     * Name
     * Change key name of cookie
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
     * Return all cookies
     *
     * @return array
     */
    public function all() {
        return $_COOKIE;
    }

    /**
     * Destroy the cookie
     *
     */
    public function destroy() {
        return $_COOKIE = [];
    }

}