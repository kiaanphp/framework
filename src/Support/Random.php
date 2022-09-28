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
| Random
|---------------------------------------------------
*/
class Random {
    
    /*
    * Global traits
    *
    */
    use \Kiaan\Application\Resources\Global\ExtendingTrait;
     
    /**
     * Generate boolen
     *
     * @return bool
    */
    public function bool()
    {
        return 1 === \rand(0, 1);
    }

    /**
     * Generate uniq id
     * @throws \Exception
     */
    public function uniqid(string $prefix = ''): string
    {
        if (true === \function_exists('random_bytes')) {
            $bytes = \random_bytes(7);
        } elseif (true === \function_exists('openssl_random_pseudo_bytes')) {
            $bytes = \openssl_random_pseudo_bytes(7);
        } else {
            throw new \Exception('No cryptographically secure random function available');
        }

        return $prefix . \mb_substr(\bin2hex($bytes), 0, 13);
    }

    /**
     * Generate random number
     *
     * @return string
    */
    public function num($min , $max){
        return rand($min, $max);
    }

    /**
     * Generate random letter uppercase
     *
     * @return string
    */
    public function letterUpper(){
        return chr(rand(65, 90));
    }
    
    /**
     * Generate random letter lowercase
     *
     * @return string
    */
    public function letterLower(){
        return chr(rand(97,122));
    }

    /**
     * Generate random digit
     *
     * @return string
    */
    public function digit($size){
        $random_number='';
        $count=0;
        while ($count < $size ) 
            {
                $random_digit = mt_rand(0, 9);
                $random_number .= $random_digit;
                $count++;
            }
        return $random_number;  
    }

    /**
     * Generate random string
     *
     * @return string
    */
    public function str($length){
        $randomBytes = openssl_random_pseudo_bytes($length);
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);
        $result = '';
        for ($i = 0; $i < $length; $i++)
            $result .= $characters[ord($randomBytes[$i]) % $charactersLength];
        return $result;
    }

    /**
     * Get a random value from an array.
     *
     * @param array $array
     * @param int   $numReq The amount of values to return
     *
     * @return mixed
     */
    public function arrayRandom(array $array, $numReq = 1)
    {
        if (! count($array)) {
            return;
        }

        $keys = array_rand($array, $numReq);

        if ($numReq === 1) {
            return $array[$keys];
        }

        return array_intersect_key($array, array_flip($keys));
    }

}