<?php

/**
 * This class reads data and stores into a Object, additionally the data
 * is trimmed and can be read as a specific type to avoid any injection.
 * @package Spitfire.security.io
 * @author  César de la Cal <cesar@magic3w.com>
 */

class _SF_InputSanitizer
{
	private $data = false;
	private $isset;
	
	public function __construct($data, $isset = true) {
		
		$this->isset = $isset;

		if (is_array($data)) {
			$this->data = Array();
			foreach ($data as $field=>$value)
				$this->data[$field] = $this->$field = new _SF_InputSanitizer($value);
		}
		else $this->data = trim($data);

	}
	
	public function value() {
		return $this->data;
	}
	
	public function toInt() {
		return (int)$this->data;
	}

	public function toBool() {
		return !!$this->data;
	}

	public function toPassword ($min_length = 3) {
		$str = $this->value();

		if (!$str || strlen($str) < $min_length) return false;
		$salt   = 'unyc24jgOKCWJSGnAfil';
		$pepper = 'NxknOQPE9fievlPbhtiG';
		$pass   = $str;
		
		return md5("$pepper$pass$salt");
	}
	
	public function is_set() {
		return $this->isset;
	}

	public function toArray () {
		if ( is_array($this->data) ) return $this->data;
		else return false;
	}
	
	public function __get($value) {
		return $this->$value = new _SF_InputSanitizer(false, false);
	}
	
	public function __isset($name) {
		return $this->{$name}->is_set();
	}


	public function __toString() {
		return $this->value();
	}
	
}