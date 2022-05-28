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
namespace Kiaan\Contact\Socket\Addons\React\EventLoop;

/*
|---------------------------------------------------
| Class
|---------------------------------------------------
*/
interface TimerInterface
{
    /**
     * Get the interval after which this timer will execute, in seconds
     *
     * @return float
     */
    public function getInterval();

    /**
     * Get the callback that will be executed when this timer elapses
     *
     * @return callable
     */
    public function getCallback();

    /**
     * Determine whether the time is periodic
     *
     * @return bool
     */
    public function isPeriodic();
}
