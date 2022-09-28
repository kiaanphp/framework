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
namespace Kiaan\Http;

/*
|---------------------------------------------------
| Uses
|---------------------------------------------------
*/
use Kiaan\Http\Input\File;

/*
|---------------------------------------------------
| Input
|---------------------------------------------------
*/
class Input {
    
    /*
    * Global traits
    *
    */
    use \Kiaan\Application\Resources\Global\ExtendingTrait;
    use \Kiaan\Application\Resources\Global\FilesystemPathTrait;
     

    /**
     * Get
     * 
    */
    protected $get;

    /**
     * Post
     * 
    */
    protected $post;

    /**
     * Files
     * 
    */
    protected $files;

    /**
     * Request
     * 
    */
    protected $request;
      
    /**
     * Constructor of class
     *
     * @return void
     */
    public function __construct(){
        // Get
        $this->get = $_GET;

        // Post
        $raw = json_decode(file_get_contents('php://input'), true) ?? array();
        $this->post = array_merge($_POST, $raw);

        // Files
        $this->files = $_FILES;

        // Request
        $this->request = array_merge($_REQUEST, $_FILES);
    }

    /*
     * Magic functions
     *
     */
    public function __set($key, $value){
        return $this->set($key, $value);
    }

    public function __get($key){
        return $this->value($key);
    }

	/**
	 * Returns an iterator for the elements.
	 *
	 * @return \Iterator Over map elements
	 */
	public function getIterator() : \Iterator
	{
		return new \ArrayIterator( $this->request );
	}

	/**
	 * Determines if an element exists at an offset.
	 * 
	 * @param mixed $key Key to check for
	 * @return bool TRUE if key exists, FALSE if not
	 */
	public function offsetExists( $key )
	{
		return isset( $this->request[$key] );
	}

	/**
	 * Returns an element at a given offset.
	 * 
	 * @param mixed $key Key to return the element for
	 * @return mixed Value associated to the given key
	 */
	public function offsetGet( $key )
	{
		return $this->request[$key] ?? null;
	}

	/**
	 * Sets the element at a given offset.
	 *
	 * @param mixed $key Key to set the element for
	 * @param mixed $value New value set for the key
	 */
	public function offsetSet( $key, $value )
	{
		if( $key !== null ) {
			$this->request[$key] = $value;
		} else {
			$this->request[] = $value;
		}
	}

	/**
	 * Unsets the element at a given offset.
	 *
	 * @param string $key Key for unsetting the item
	 */
	public function offsetUnset( $key )
	{
		unset( $this->request[$key] );
	}
  
    /**
    * Get all request
    *
    */
    public function all() {
        return $this->request;
    }

    /**
    * Check that the request has the key
    *
    */
    public function has($key, array $request=null) {
        $request = (!is_array($request)) ? $this->request : $request;

        return array_key_exists($key, $request);
    }

    /**
    * Check that the $_GET request has the key
    *
    */
    public function hasGet($key) {
        return $this->has($key, $this->get);
    }

    /**
    * Check that the $_POST request has the key
    *
    */
    public function hasPost($key) {
        return $this->has($key, $this->post);
    }

    /**
    * Check that the $_FILES request has the key
    *
    */
    public function hasFile($key) {
        return $this->has($key, $this->files);
    }
    
    /**
     * Get the value from the request
     *
     */
    public function value($key, array $request=null) {
        $request = (!is_array($request)) ? $this->request : $request;

        return ($this->has($key)) ? $request[$key] : false;
    }

    /**
     * Get the value from the $_GET request
     *
     */
    public function get($key) {
        return $this->value($key, $this->get);
    }

    /**
     * Get the value from the $_POST request
     *
     */
    public function post($key) {
        return $this->value($key, $this->post);
    }

    /**
     * Set value for request by the given key
     *
     */
    public function set($key, $value) {
        if($this->has($key, $this->get)){ $this->get[$key] = $value; }
        if($this->has($key, $this->post)){ $this->post[$key] = $value; }
        if($this->has($key, $this->files)){ $this->files[$key]['name'] = $value; }
        if($this->has($key, $this->request)){ $this->request[$key] = $value; }

        return ($this->has($key)) ? $this->request[$key] : false;
    }

    /**
     * Select file
     *
     * @param string $key
     * @return string $value
     */
    public function file($key) {
        if($this->has($key, $this->files)){
          return new File($this, $this->files, $key);
        }

        return false;
    }   

}