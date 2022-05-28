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
namespace Kiaan\Contact\Socket\Addons\React\Promise\Timer;

/*
|---------------------------------------------------
| Uses
|---------------------------------------------------
*/
use RuntimeException;

/*
|---------------------------------------------------
| Class
|---------------------------------------------------
*/
class TimeoutException extends RuntimeException
{
    private $timeout;

    public function __construct($timeout, $message = null, $code = null, $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->timeout = $timeout;
    }

    public function getTimeout()
    {
        return $this->timeout;
    }
}
