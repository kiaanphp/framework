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
namespace Kiaan\Lang\Trans;

/*
|---------------------------------------------------
| Loader
|---------------------------------------------------
*/
class Loader {
    
    /**
     *  data
     * 
    */
    public $data;

    /**
     * Constructor
     * 
    **/
    public function __construct($data) {
        $this->data = $data;
    }

    /**
     * Return all
     *
     * @return array
    */
    public function all() {
        return $this->data;
    }

    /*
    * Get
    *
    */
    public function get($key, $vars=[], $default=null)
    {
		$array = $this->data;

        if (is_string($key) && is_array($array)) {
            $keys = explode('.', $key);

            while (sizeof($keys) >= 1) {
                $k = array_shift($keys);

                if (!isset($array[$k])) {
                    if(! is_callable($default)){
                        return $default;
                    }else{
                        return call_user_func($default);
                    }
                }

                if (sizeof($keys) === 0) {
                    return $this->prepareValueWithVars($array[$k], $vars);
                }

                $array = &$array[$k];
            }
        }

        return $this;
    }

    /*
    * Prepare value with vars
    *
    */
    protected function prepareValueWithVars($value, $vars)
    {
        $t = $value;
        
        // Escape
        preg_match_all('~\\\{{(.*?)\}}~si', $t, $escape);
        $escape = $escape[1];

        // Matches
        preg_match_all('~\{{(.*?)\}}~si', $t, $matches);
        $matches = array_diff($matches[1], $escape);

        if ( isset($matches)) {
            foreach ( $matches as $var => $value ) {
                $t = str_replace('{{' . $value . '}}', $vars[$value], $t);
            }
        }

        // Escape "\{{" to "{{"
        $t = str_replace('\{{', "{{", $t);

        return $t;
    }

    /*
    * Set
    *
    */
    public function set($key, $value)
    {
		$array = $this->data;

        if (is_string($key) && !empty($key)) {

            $keys = explode('.', $key);
            $arrTmp = &$array;

            while (sizeof($keys) >= 1) {
                $k = array_shift($keys);

                if (!is_array($arrTmp)) {
                    $arrTmp = [];
                }

                if (!isset($arrTmp[$k])) {
                    $arrTmp[$k] = [];
                }

                if (sizeof($keys) === 0) {
                    $arrTmp[$k] = $value;
					$this->data = $arrTmp;
                }

                $arrTmp = &$arrTmp[$k];
            }
        }
        return $this;
    }

    /*
    * Has
    */
    public function has($key, $default=null)
    {
		$array = $this->data;

        if (is_string($key) && is_array($array)) {
            $keys = explode('.', $key);

            while (sizeof($keys) >= 1) {
                $k = array_shift($keys);

                if (!isset($array[$k])) {
                    return false;
                }

                if (sizeof($keys) === 0) {
                    return true;
                }
            }
        }

        return $this;
    }

}