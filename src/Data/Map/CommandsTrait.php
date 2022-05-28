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
namespace Kiaan\Data\Map;

/*
|---------------------------------------------------
| Uses
|---------------------------------------------------
*/
use Closure;
use BadMethodCallException;

/*
|---------------------------------------------------
| Commands trait
|---------------------------------------------------
*/
trait CommandsTrait
{

    /**
     * The registered string Commands.
     *
     * @var array
     */
    protected static $commands = [];

    /**
     * Register a custom Command.
     *
     * @param  string   $name
     * @param  callable $command
     *
     * @return void
     */
    public static function command($name, callable $command)
    {
        static::$commands[$name] = $command;
    }

    /**
     * Checks if Command is registered.
     *
     * @param  string $name
     *
     * @return bool
     */
    public static function hasCommand($name)
    {
        return isset(static::$commands[$name]);
    }

    /**
     * Dynamically handle calls to the class.
     *
     * @param  string $method
     * @param  array  $parameters
     *
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    public static function __callStatic($method, $parameters)
    {
        if (static::hasCommand($method)) {
            if (static::$commands[$method] instanceof Closure) {
                return call_user_func_array(Closure::bind(static::$commands[$method], null, get_called_class()),
                    $parameters);
            } else {
                return call_user_func_array(static::$commands[$method], $parameters);
            }
        }

        throw new BadMethodCallException("Method {$method} does not exist.");
    }

    /**
     * Dynamically handle calls to the class.
     *
     * @param  string $method
     * @param  array  $parameters
     *
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        if (static::hasCommand($method)) {
            if (static::$commands[$method] instanceof Closure) {
                return call_user_func_array(static::$commands[$method]->bindTo($this, get_class($this)), $parameters);
            } else {
                return call_user_func_array(static::$commands[$method], $parameters);
            }
        }

        throw new BadMethodCallException("Method {$method} does not exist.");
    }

}