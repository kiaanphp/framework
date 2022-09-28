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
namespace Kiaan\Dev;

/*
|---------------------------------------------------
| Classes
|---------------------------------------------------
*/
class Classes {
    
    /*
    * Global traits
    *
    */
    use \Kiaan\Application\Resources\Global\ExtendingTrait;
     
    /**
	 * Alias
     * 
	 * Creates an alias for a class
	*/
    public function alias(string $original, string $alias)
    {
        return class_alias($original, $alias, true);
    }

    /**
	 * Exists
     * 
	 * Checks if the class has been defined
	*/
    public function exists(string $class)
    {
        return class_exists($class);
    }
    
    /**
	 * Trait exists 
     * 
	 * Checks if the trait exists
	*/
    public function traitExists(string $traitname)
    {
        return trait_exists($traitname);
    }

    /**
	 * Interface exists 
     * 
	 * Checks if the interface has been defined
	*/
    public function interfaceExists(string $class)
    {
        return interface_exists($class);
    }

    /**
	 * Method exists 
     * 
	 * Checks if the class method exists
	*/
    public function methodExists($object , $method_name)
    {
        return method_exists($object, $method_name);
    }

    /**
	 * Property exists 
     * 
	 * Checks if the object or class has a property
	*/
    public function propertyExists($class, $property)
    {
        return property_exists($class, $property);
    }

    /**
	 * Class
     * 
	 * Returns the name of the class of an object or get name my class
	*/
    public function class($object='')
    {
        if(empty($object)){
            return get_called_class();
        }else {
            return get_class($object);
        }
    }

    /**
	 * Parent
     * 
	 * Retrieves the parent class name for object or class
	*/
    public function parent($object)
    {
        return get_parent_class($object);
    }

    /**
	 * Constructor
     * 
	 * Gets the constructor of the class
	*/
    public function constructor($class_name)
    {
        $class = new \ReflectionClass($class_name);
        $res = $class->getConstructor();

        return $res;
    }
    
    /**
	 * Properties
     * 
	 * Gets properties
     * 
     * Filter (null| static | public | protected | private)
	*/ 
    public function properties($class_name, $filter=null)
    {
        // Filter
        $filter = str_replace(['static'], \ReflectionProperty::IS_STATIC, $filter);
        $filter = str_replace(['public'], \ReflectionProperty::IS_PUBLIC, $filter);
        $filter = str_replace(['protected'], \ReflectionProperty::IS_PROTECTED, $filter);
        $filter = str_replace(['private'], \ReflectionProperty::IS_PRIVATE, $filter);
    
        $class = new \ReflectionClass($class_name);

        if(empty($filter)){
            $res = $class->getProperties(null);
        }else {
            $res = $class->getProperties($filter);
        }

        return $res;
    }

    /**
	 * Default properties
     * 
	 * Gets default properties
	*/
    public function defaultProperties($class_name)
    {
        $class = new \ReflectionClass($class_name);
        $res = $class->getDefaultProperties();

        return $res;
    }

    /**
	 * File
     * 
	 * Gets the filename of the file in which the class has been defined
	*/
    public function file($class_name)
    {
        $class = new \ReflectionClass($class_name);
        $res = $class->getFileName();

        return $res;
    } 

    /**
	 * Interface
     * 
	 * Gets the interface names
	*/
    public function interface($class_name)
    {
        $class = new \ReflectionClass($class_name);
        $res = $class->getInterfaceNames();

        return $res;
    }  
    
    /**
	 * Methods
     * 
	 * Gets an array of methods
     * Filter (static | public | protected | private)
     * 
     * Example : (new Classes)->methods('class_name', 'protected | public')
	*/ 
    public function methods($class_name, $filter=null)
    {
        // Filter
        $filter = str_replace(['static'], \ReflectionProperty::IS_STATIC, $filter);
        $filter = str_replace(['public'], \ReflectionProperty::IS_PUBLIC, $filter);
        $filter = str_replace(['protected'], \ReflectionProperty::IS_PROTECTED, $filter);
        $filter = str_replace(['private'], \ReflectionProperty::IS_PRIVATE, $filter);
    
        $class = new \ReflectionClass($class_name);

        if(empty($filter)){
            $res = $class->getMethods(null);
        }else {
            $res = $class->getMethods($filter);
        }

        return $res;
    }

    /**
	 * Constants
     * Gets class constants
	*/
    public function constants($class_name)
    {
        $class = new \ReflectionClass($class_name);
        $res = $class->getReflectionConstants ();

        return $res;
    }
    
    /**
	 * Get
     * 
     * Gets name of class with namespace
	*/
    public function get($class_name)
    {
        $class = new \ReflectionClass($class_name);
        $res = $class->getName();

        return $res;
    }

    /**
	 * Name
     * 
     * Gets short name
	*/
    public function name($class_name)
    {
        $class = new \ReflectionClass($class_name);
        $res = $class->getShortName();

        return $res;
    }

    /**
	 * Namespace
     * 
     * Gets namespace name
	*/
    public function namespace($class_name)
    {
        $class = new \ReflectionClass($class_name);
        $res = $class->getNamespaceName();

        return $res;
    }

    /**
	 * Traits
     * Returns an array of traits used by this class
	*/
    public function traits($class_name)
    {
        $class = new \ReflectionClass($class_name);
        $res = $class->getTraits();

        return $res;
    }

    /**
	 * Has constant
     * Checks if constant is defined
	*/
    public function hasConstant($class_name, $name)
    {
        $class = new \ReflectionClass($class_name);
        $res = $class->hasConstant($name);

        return $res;
    }

    /**
	 * Has method
     * Checks if method is defined
	*/
    public function hasMethod($class_name, $name)
    {
        $class = new \ReflectionClass($class_name);
        $res = $class->hasMethod($name);

        return $res;
    }

    /**
	 * Has property
     * Checks if property is defined
	*/
    public function hasProperty($class_name, $name)
    {
        $class = new \ReflectionClass($class_name);
        $res = $class->hasProperty($name);

        return $res;
    }

    /**
	 * Is abstract
     * Checks if class is abstract
	*/
    public function isAbstract($class_name)
    {
        $class = new \ReflectionClass($class_name);
        $res = $class->isAbstract();

        return $res;
    }

    /**
	 * Is anonymous
     * Checks if class is anonymous
	*/
    public function isAnonymous($class_name)
    {
        $class = new \ReflectionClass($class_name);
        $res = $class->isAnonymous();

        return $res;
    }

    /**
	 * Is cloneable
     * Returns whether this class is cloneable
	*/
    public function isCloneable($class_name)
    {
        $class = new \ReflectionClass($class_name);
        $res = $class->isCloneable();

        return $res;
    }

    /**
	 * Is final
     * Checks if class is final
	*/
    public function isFinal($class_name)
    {
        $class = new \ReflectionClass($class_name);
        $res = $class->isFinal();

        return $res;
    }

    /**
	 * Is instantiable
     * Checks if the class is instantiable
	*/
    public function isInstantiable($class_name)
    {
        $class = new \ReflectionClass($class_name);
        $res = $class->isInstantiable();

        return $res;
    }

    /**
	 * Is nterface
     * Checks if the class is an interface
	*/
    public function isInterface($class_name)
    {
        $class = new \ReflectionClass($class_name);
        $res = $class->isInterface();

        return $res;
    }

    /**
	 * Is internal
     * Checks if class is defined internally by an extension, or the core
	*/
    public function isInternal($class_name)
    {
        $class = new \ReflectionClass($class_name);
        $res = $class->isInternal();

        return $res;
    }

    /**
	 * Is iterable
     * Check whether this class is iterable
	*/
    public function isIterable($class_name)
    {
        $class = new \ReflectionClass($class_name);
        $res = $class->isIterable();

        return $res;
    }

    /**
	 * Is sub-class
     * Checks if a subclass
	*/
    public function isSubClass($class_name, $class)
    {
        $class = new \ReflectionClass($class_name);
        $res = $class->isSubclassOf($class);

        return $res;
    }

    /**
	 * Is sub-class
     * Returns whether this is a trait
	*/
    public function isTrait($class_name)
    {
        $class = new \ReflectionClass($class_name);
        $res = $class->isTrait();

        return $res;
    }
    
    /**
	 * New
     * Creates a new class instance from given arguments
	*/
    public function new($class_name, $arguments=[])
    {
        $class = new \ReflectionClass($class_name);
        $res = $class->newInstance(...$arguments);

        return $res;
    }

    /**
	 * Instance
     * Creates a new class instance without invoking the constructor
	*/
    public function instance($class_name)
    {
        $class = new \ReflectionClass($class_name);
        $res = $class->newInstanceWithoutConstructor();

        return $res;
    }

}