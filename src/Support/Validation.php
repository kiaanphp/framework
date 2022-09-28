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
namespace Kiaan\Support;

/*
|---------------------------------------------------
| Validation
|---------------------------------------------------
*/
class Validation {
    
    /*
    * Global traits
    *
    */
    use \Kiaan\Application\Resources\Global\ExtendingTrait;
     
    /**
     * Validated email address.
     *
     * @param $email
     *
     * @return bool
     */
    public function email($email)
    {
        return (bool)\filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Validates JSON input.
     *
     * @param $string
     *
     * @return bool
     */
    public function json($string)
    {
        if (\is_int($string) || \is_float($string)) {
            return true;
        }

        \json_decode($string);

        return JSON_ERROR_NONE === \json_last_error();
    }

    /**
     * Checks if the given value is empty. This method is useful for PHP <= 5.4,
     * where you cannot pass function returns directly into empty() eg. empty(date('Y-m-d')).
     *
     * @param $value
     *
     * @return bool
     */
    public function empty($value)
    {
        return empty($value);
    }

    /**
     * Checks if given value is of type "integer".
     *
     * @param $value
     *
     * @return bool
     */
    public function integer($value)
    {
        if (\is_int($value)) {
            return true;
        } elseif (!\is_string($value)) {
            return false;
        }

        return \preg_match('/^\d+$/', $value) > 0;
    }

    /**
     * Checks if given value is of type "float".
     *
     * @param $value
     *
     * @return bool
     */
    public function float($value)
    {
        if (\is_float($value)) {
            return true;
        } elseif (!\is_string($value)) {
            return false;
        }

        return \preg_match('/^[0-9]+\.[0-9]+$/', $value) > 0;
    }
  
    /**
     * Check if value is boolen.
     * @param $value
     */
    public function bool($value): bool
    {
        return \is_bool($value);
    }

    /**
     * Check if value is TRUE.
     *
     * @param $value
     *
     * @return bool
     */
    public function true($value)
    {
        return \is_bool($value) && true === $value;
    }

    /**
     * * Check if value is FALSE.
     *
     * @param $value
     *
     * @return bool
     */
    public function false($value)
    {
        return \is_bool($value) && false === $value;
    }

    /**
     * Check if it's an array
     * @param $value
     */
    public function array($value): bool
    {
        return \is_array($value);
    }

    /**
     * Check if it's an double
     * @param $value
     */
    public function double($value): bool
    {
        return \is_double($value);
    }

    /**
     * Check if it's an integer
     * @param $value
     */
    public function int($value): bool
    {
        return \is_int($value);
    }

    /**
     * Check if it's an null
     * @param $value
     */
    public function null($value): bool
    {
        return \is_null($value);
    }

    /**
     * Check if it's an numeric
     * @param $value
     */
    public function numeric($value): bool
    {
        return \is_numeric($value);
    }

    /**
     * Check if it's an string
     * @param $value
     */
    public function string($value): bool
    {
        return \is_string($value);
    }
    
    /**
     * Check if it's an url
     * @param $value
     */
    public function url($value): bool
    {
        return \filter_var($value, FILTER_VALIDATE_URL);
    }
    
    /**
     * Checks if a string is blank. " " is considered as such.
     *
     * @param $string
     *
     * @return bool
     */
    public function blank($value)
    {
        return !\strlen(\trim((string)$value)) > 0;
    }

    /**
     * Check if the client is mobile device
     *
     * @return bool
    */
    public function mobile(): bool
    {
        return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
    }

    /**
     * Check if the client is Android
     *
     * @return bool
    */
    public function android(): bool
    {
        return (strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false);
    }

    /**
     * Check if the client is Windows
     *
     * @return bool
    */
    public function windows(): bool
    {
        /** @noinspection ReturnFalseInspection */
        return false !== \strpos($_SERVER['HTTP_USER_AGENT'], 'Win');
    }

    /**
     * Check if the client is Macintosh
     *
     * @return bool
    */
    public function mac(): bool
    {
        /** @noinspection ReturnFalseInspection */
        return false !== \strpos($_SERVER['HTTP_USER_AGENT'], 'mac');
    }

    /**
     * Returns a value indicating whether the given array is an associative array.
     *
     * An array is associative if all its keys are strings. If `$allStrings` is false,
     * then an array will be treated as associative if at least one of its keys is a string.
     *
     * Note that an empty array will NOT be considered associative.
     *
     * @param array $array the array being checked
     * @param bool $allStrings whether the array keys must be all strings in order for
     * the array to be treated as associative.
     * @return bool whether the array is associative
     */
    public function arrayAssoc($array)
    {
        $allStrings = true;
        
        if (!is_array($array) || empty($array)) {
            return false;
        }

        if ($allStrings) {
            foreach ($array as $key => $value) {
                if (!is_string($key)) {
                    return false;
                }
            }

            return true;
        }

        foreach ($array as $key => $value) {
            if (is_string($key)) {
                return true;
            }
        }

        return false;
    }

     /**
     * Returns a value indicating whether the given array is an indexed array.
     *
     * An array is indexed if all its keys are integers. If `$consecutive` is true,
     * then the array keys must be a consecutive sequence starting from 0.
     *
     * Note that an empty array will be considered indexed.
     *
     * @param array $array the array being checked
     * @param bool $consecutive whether the array keys must be a consecutive sequence
     * in order for the array to be treated as indexed.
     * @return bool whether the array is indexed
     */
    public function arrayIndexed($array)
    {
        $consecutive = false;

        if (!is_array($array)) {
            return false;
        }

        if (empty($array)) {
            return true;
        }

        if ($consecutive) {
            return array_keys($array) === range(0, count($array) - 1);
        }

        foreach ($array as $key => $value) {
            if (!is_int($key)) {
                return false;
            }
        }

        return true;
    }

}