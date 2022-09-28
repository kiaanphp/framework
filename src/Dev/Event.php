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
namespace Kiaan\Dev;

/*
|---------------------------------------------------
| Event
|---------------------------------------------------
*/
class Event
{

    /*
    * Global traits
    *
    */
    use \Kiaan\Application\Resources\Global\ExtendingTrait;

    /*
    * Events list
    */
    private $events = [];

    /*
    * Error message list
    */
    protected $errorMessage = [
        'callback' => 'Invalid callback',
        'array' => 'Array required',
        'listener' => 'Listener not defined',
    ];

    /*
    * Namespace
    */
    protected $namespace;

    /*
    * path
    */
    protected $path;

    
     /**
     * Get namespace class
     */
    public function getNamespace()
    {
        return $this->namespace;
    }  

     /**
     * Set namespace class
     */
    public function setNamespace(string $namespace)
    {
        $this->namespace = $namespace;
    }

     /**
     * Get path
     */
    public function getPath()
    {
        return $this->path;
    } 

     /**
     * Set path
     */
    public function setPath(string $path)
    {
        $this->path = $path;
    } 

    /**
     * 
     */
    public function listen($name, $callback)
    {
        if(is_array($callback)){
            $class = $callback[0] ?? "";
            $method = $callback[1] ?? "";
        }

        if(is_string($callback)){
            $callback = explode(",", trim($callback));
            $class = $callback[0] ?? "";
            $method = $callback[1] ?? "";
        }
        
        $class = trim($class);
        $method = trim($method);

        if(!class_exists($class)){
            $class = trim($this->namespace, '\\') . '\\' . $class;
        }

        $callback = array(new $class, $method);

        if (!is_callable($callback)) {
            throw new \Exception($this->errorMessage['callback']);
        }

        $this->events[$name][] = $callback;

        return true;
    }

    /**
     * 
     */
    public function listeners($names, $callback)
    {
        $result = true;

        if (!is_array($names)) {
            throw new \Exception($this->errorMessage['array']);
        }

        foreach ($names as $name) {
            $result = $this->listen($name, $callback);
        }

        return $result;
    }

    /**
     * 
     */
    public function trigger($name, $argument = null)
    {
        $name = trim($name);

        if (!isset($this->events[$name])) {
            throw new \Exception($this->errorMessage['listener']);
        }

        foreach ($this->events[$name] as $event => $callback) {
            if($argument && is_array($argument)) {
                call_user_func_array($callback, $argument);
            }
            elseif ($argument && !is_array($argument)) {
                call_user_func($callback, $argument);
            }
            else {
                call_user_func($callback);
            }
        }

        return true;
    }

    /**
     * 
     */
    public function remove($name)
    {
        if (is_array($name)) {
            foreach ($name as $n) {
                $result = $this->remove($n);

                if (!$result) {
                    return $result;
                }
            }

            return true;
        } else {
            if (isset($this->events[$name])) {
                unset($this->events[$name]);

                return true;
            }
        }

        return false;
    }

    /**
     * Reset all events
     *
     * @return bool
     */
    public function reset()
    {
        $this->events = [];

        return true;
    }
}