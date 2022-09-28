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
    
    /*
    * Global traits
    *
    */
    use \Kiaan\Application\Resources\Global\ExtendingTrait;
     
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
     * Change key name of cookie
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
     * Destroy the cookie
     *
     */
    public function destroy() {
        return $_COOKIE = [];
    }

    /**
     * Return array for all cookies
    * 
    */
    public function toArray() {
        return $_COOKIE;
    }
  
    /**
     * Return object for all cookies
     * 
    */
    public function toObject() {
        return json_decode(json_encode($this->toArray()), false);
    }

    /**
     * Return object for all cookies
     * 
     */
    public function all() {
        return $this->toObject();
    }

}