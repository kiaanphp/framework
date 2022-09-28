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
| Math
|---------------------------------------------------
*/
class Math {
    
    /*
    * Global traits
    *
    */
    use \Kiaan\Application\Resources\Global\ExtendingTrait;
     
    /**
     * Counts an average value from a list of values
     * @param array $values (number)
     * @return float
    */
    public function average(array $values)
    {
        return array_sum($values) / count($values);
    }

    /**
     * Get the sign of the given number, indicating whether the number is positive, negative or zero
     *
     * @param integer|float $x The value
     * @return integer -, 0, + depending on the sign or NAN if the given value was not numeric
     */
    public function sign($x)
    {
        if ($x < 0) {
            return '-';
        } elseif ($x > 0) {
            return '+';
        } elseif ($x === 0 || $x === 0.0) {
            return 0;
        } else {
            return NAN;
        }
    }

    /**
     * returns the positive value of a number.
     *
     */
    public function positive($x)
    {
        return abs($x);
    }

    /**
     * returns the negative value of a number.
     *
     */
    public function negative($x)
    {
        return -abs($x);
    }

    /**
     * Changing the sign of a number.
     *
     */
    public function signExchange($x)
    {
        return $x <= 0 ? abs($x) : -$x ;
    }

}