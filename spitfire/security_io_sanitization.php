<?php namespace spitfire;

/**
 * This class reads data and stores into a Object, additionally the data
 * is trimmed and can be read as a specific type to avoid any injection.
 * 
 * The sanitizer is one of the oldest and most obsolete classes in the system and
 * was begging to be freed of it's own terror
 * 
 * @deprecated since version 0.1-dev 20160116
 * @package Spitfire.security.io
 * @author  César de la Cal <cesar@magic3w.com>
 */

class InputSanitizer
{
	private $data = false;
	private $isset;
	
	public function __construct($data, $isset = true) {
		
		$this->isset = $isset;

		if (is_array($data) || $data instanceof \Iterator) {
			$this->data = Array();
			foreach ($data as $field=>$value) {
				$this->data[$field] = new InputSanitizer($value);
			}
		}
		else { $this->data = $data; }

	}
	
	public function value() {
		return is_string($this->data)? filter_var($this->data, FILTER_SANITIZE_STRING) : $this->data;
	}
	
	public function toInt() {
		return (int)$this->data;
	}

	public function toBool() {
		return !!$this->data;
	}
	
	public function is_set() {
		return $this->isset;
	}

	public function toArray () {
		if ( is_array($this->data) ) return $this->data;
		else return false;
	}
	
	public function __get($value) {
		return $this->$value = new InputSanitizer(false, false);
	}
	
	public function __isset($name) {
		return $this->{$name}->is_set();
	}


	public function __toString() {
		return $this->value();
	}
	
}