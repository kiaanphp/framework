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
| Time
|---------------------------------------------------
*/
class Time {
    
    /*
    * Global traits
    *
    */
    use \Kiaan\Application\Resources\Global\ExtendingTrait;
     
    /**
     * returns year
     * examble : Time::readable(time());
     * 
    */
    public function readable($time)
    {
        return date('Y-m-d H:i:s', $time);
    }

    /**
     * returns number to month name
     * examble : Time::monthName(1);
     *
    */
    public function monthName($num)
    {
        return date("F", mktime(0, 0, 0, $num, 10));
    }

    /**
     * Converting timestamp to time ago e.g 1 day ago, 2 days ago…
     * examble : Time::since(time());
     * 
    */
    public function since($ptime) {
        $etime = time() - $ptime;

        if ($etime < 1)
        {
            return '0 seconds';
        }
    
        $a = array( 365 * 24 * 60 * 60  =>  'year',
                     30 * 24 * 60 * 60  =>  'month',
                          24 * 60 * 60  =>  'day',
                               60 * 60  =>  'hour',
                                    60  =>  'minute',
                                     1  =>  'second'
                    );

        $a_plural = array( 'year'   => 'years',
                           'month'  => 'months',
                           'day'    => 'days',
                           'hour'   => 'hours',
                           'minute' => 'minutes',
                           'second' => 'seconds'
                    );
    
        foreach ($a as $secs => $str)
        {
            $d = $etime / $secs;
            if ($d >= 1)
            {
                $r = round($d);
                return $r . ' ' . ($r > 1 ? $a_plural[$str] : $str) . ' ago';
            }
        }
    }

}