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
| Number
|---------------------------------------------------
*/
class Number {
    
    /*
    * Global traits
    *
    */
    use \Kiaan\Application\Resources\Global\ExtendingTrait;
     
    /**
     * Converts number to its ordinal English form. For example, converts 13 to 13th, 2 to 2nd etc.
     *
     * @param float|int|string $value The number to get its ordinal value.
     *
     * @return string
     */
    public function ordinal($value){
        if (!is_numeric($value)) {
            $type = gettype($value);
            throw new \InvalidArgumentException("Value must be numeric. $type given.");
        }

        if (fmod((float)$value, 1) !== 0.00) {
            return (string)$value;
        }

        if (\in_array($value % 100, [11, 12, 13], true)) {
            return $value . 'th';
        }
        switch ($value % 10) {
            case 1:
                return $value . 'st';
            case 2:
                return $value . 'nd';
            case 3:
                return $value . 'rd';
            default:
                return $value . 'th';
        }
    }

    /**
     * Returns string representation of a number value without thousands separators and with dot as decimal separator.
     *
     * @param float|int|string $value
     *
     * @return string
     */
    public function normalize($value){
        /**
         * @psalm-suppress DocblockTypeContradiction
         */
        if (!is_scalar($value)) {
            $type = gettype($value);
            throw new \InvalidArgumentException("Value must be scalar. $type given.");
        }
        $value = str_replace([' ', ','], ['', '.'], (string)$value);
        return preg_replace('/\.(?=.*\.)/', '', $value);
    }

    /**
     * Number format
     *  
     * @return string
     */
    public function format($value){
        return number_format($value);
    }

    /**
     * convert number to word
     *
     * @return string
     */
    public function toWord($num=false)
    {
        $num = str_replace(array(',', ' '), '' , trim($num));
        if(! $num) {
            return false;
        }
        $num = (int) $num;
        $words = array();
        $list1 = array('', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven',
            'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'
        );
        $list2 = array('', 'ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety', 'hundred');
        $list3 = array('', 'thousand', 'million', 'billion', 'trillion', 'quadrillion', 'quintillion', 'sextillion', 'septillion',
            'octillion', 'nonillion', 'decillion', 'undecillion', 'duodecillion', 'tredecillion', 'quattuordecillion',
            'quindecillion', 'sexdecillion', 'septendecillion', 'octodecillion', 'novemdecillion', 'vigintillion'
        );
        $num_length = strlen($num);
        $levels = (int) (($num_length + 2) / 3);
        $max_length = $levels * 3;
        $num = substr('00' . $num, -$max_length);
        $num_levels = str_split($num, 3);
        for ($i = 0; $i < count($num_levels); $i++) {
            $levels--;
            $hundreds = (int) ($num_levels[$i] / 100);
            $hundreds = ($hundreds ? ' ' . $list1[$hundreds] . ' hundred' . ' ' : '');
            $tens = (int) ($num_levels[$i] % 100);
            $singles = '';
            if ( $tens < 20 ) {
                $tens = ($tens ? ' ' . $list1[$tens] . ' ' : '' );
            } else {
                $tens = (int)($tens / 10);
                $tens = ' ' . $list2[$tens] . ' ';
                $singles = (int) ($num_levels[$i] % 10);
                $singles = ' ' . $list1[$singles] . ' ';
            }
            $words[] = $hundreds . $tens . $singles . ( ( $levels && ( int ) ( $num_levels[$i] ) ) ? ' ' . $list3[$levels] . ' ' : '' );
        } //end for loop
        $commas = count($words);
        if ($commas > 1) {
            $commas = $commas - 1;
        }
        return implode(' ', $words);
    }

}