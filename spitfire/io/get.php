<?php namespace spitfire\io;

use Iterator;
use ArrayAccess;

/**
 * This class is meant to wrap around the _GET array. This allows Spitfire to 
 * register read and write data of your application for canonicalization of URLs.
 * Canonical URLs are important for your application to report the most recommended
 * way of accessing a certain content it provides.
 * 
 * Canonical get has been especially tricky, as many applications simply will 
 * return the current page as canonical. Using the entire content of the GET 
 * variable for that.
 * 
 * An attacker could missuse your app to generate hundreds, thousands or even millions
 * of URLs that point to the same content and use search engines to harm the 
 * performance and SEO of your app.
 * 
 * It will also trim the contents of the Array. So, it may modify data that relies
 * on trailing spaces, newline characters or similar.
 */
class Get implements Iterator, ArrayAccess
{
	/**
	 * The actual data that this element wraps. It will be provided to an array 
	 * like interface (it actually mainly implements ArrayAccess and Iterator by 
	 * calling the functions on the array) that you can use to access the data the
	 * visitor sent with his request.
	 *
	 * @var mixed
	 */
	private $data;
	
	/**
	 * The list of values your application has read from this object. It will 
	 * contain the indexes of the data array that have been requested from this
	 * object. It is used to generate the canonical array of data for the request.
	 * 
	 * @var mixed 
	 */
	private $used;
	
	/**
	 * Reads in the data that the GET variable holds. This allows it to trim the 
	 * data and replace the _GET object in the global scope.
	 * 
	 * @param mixed $data
	 */
	public function __construct($data) {
		$this->data = array_map(Array($this, 'sanitize'), $data);
	}
	
	/**
	 * Parses the content of a _GET array to fetch the data the user sent. This 
	 * function converts Arrays into another Object to make the canonicalization
	 * work on different nesting levels.
	 * 
	 * @param  \spitfire\io\Get|string $value
	 * @return string|\spitfire\io\Get
	 */
	public function sanitize($value) {
		if     ($value instanceof Get) { return $value; }
		elseif (is_array($value))      { return new self($value); }
		else                           { return trim($value); }
	}
	
	/**
	 * Gets the canonical _GET. This is the array of data that actually was used
	 * from the Request after it was processed. Although this may not be 100%
	 * accurate it will work for most web applications and provide a level of
	 * security for parameter injection (which is not the same as SQL injection)
	 * that will work for most of Spitfire based applications.
	 * 
	 * @return mixed
	 */
	public function getCanonical() {
		$_ret = Array();
		
		foreach ($this->used as $key) {
			#Check whether the object is another Get.
			if ($this->data[$key] instanceof Get) {
				$_ret[$key] = $this->data[$key]->getCanonical();
			#Check if the object is set at all. 
			} elseif (isset($this->data[$key])) {
				$_ret[$key] = $this->data[$key];
			}
		}
		
		return $_ret;
	}
	
	public function getRaw() {
		$_ret = Array();
		
		foreach ($this->data as $key => $value) {
			#Check whether the object is another Get.
			if ($value instanceof Get) {
				$_ret[$key] = $value->getRaw();
			#Check if the object is set at all. 
			} elseif (isset($this->data[$key])) {
				$_ret[$key] = $value;
			}
		}
		
		return $_ret;
	}

	public function current() {
		return current($this->data);
	}

	public function key() {
		return key($this->data);
	}

	public function next() {
		return next($this->data);
	}

	public function offsetExists($offset) {
		return isset($this->data[$offset]);
	}

	public function offsetGet($offset) {
		#If the key is found include into the array of used data.
		#This allows Spitfire to generate canonicals for you adequately.
		if (isset($this->data[$offset]) && !in_array($offset, $this->used)) {	
			$this->used[] = $offset;
		}
		return isset($this->data[$offset])? $this->data[$offset] : null;
	}

	public function offsetSet($offset, $value) {
		$this->data[$offset] = $value;
	}

	public function offsetUnset($offset) {
		if (isset($this->data[$offset])) {
			unset($this->data[$offset]);
		}
	}

	public function rewind() {
		reset($this->data);
	}

	public function valid() {
		return isset($this->data);
	}

}