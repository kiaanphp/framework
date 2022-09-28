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
namespace Kiaan\Application\Resources\Global;

/*
|---------------------------------------------------
| Uses
|---------------------------------------------------
*/
use Closure;
use BadMethodCallException;

/*
|---------------------------------------------------
| Extending trait
|---------------------------------------------------
*/
trait ExtendingTrait
{

    /**
     * The registered custom xCommands.
     *
     * @var array
     */
    protected $xCommands = [];

    /**
     * Register a custom Command.
     *
     * @param  string   $name
     * @param  callable $command
     *
     * @return void
     */
    public function xCommand($name, callable $command)
    {
        $this->xCommands[$name] = $command;
    }

    /**
     * Get list of commands.
     *
     *
     * @return bool
     */
    public function listXCommand()
    {
        return $this->xCommands;
    }

    /**
     * Checks if Command is registered.
     *
     * @param  string $name
     *
     * @return bool
     */
    public function hasXCommand($name)
    {
        return isset($this->xCommands[$name]);
    }

    /**
     * Get object from class.
     *
     *
     * @return bool
     */
    public function xThis()
    {
        return $this;
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
        if (isset(static::$xCommands[$method])) {
            if (static::$xCommands[$method] instanceof Closure) {
                return call_user_func_array(Closure::bind(static::$xCommands[$method], null, get_called_class()),
                    $parameters);
            } else {
                return call_user_func_array(static::$xCommands[$method], $parameters);
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
        if ($this->hasXCommand($method)) {
            if ($this->xCommands[$method] instanceof Closure) {
                return call_user_func_array($this->xCommands[$method]->bindTo($this, get_class($this)), $parameters);
            } else {
                return call_user_func_array($this->xCommands[$method], $parameters);
            }
        }

        throw new BadMethodCallException("Method {$method} does not exist.");
    }

}