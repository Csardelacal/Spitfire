<?php

abstract class CoffeeBean
{
	
	protected $method = "POST";
	protected $fields = Array();
	
	public function __construct() {
		if     ($this->method == "POST") $src = &$_POST;
		elseif ($this->method == "GET" ) $src = &$_GET;
		else                             $src = &$_REQUEST;
		
		foreach ($this->fields as $local => $remote) {
			#Check if valid and store
			if ($this->_validate($local, $src[$remote]))
			$this->{$local} = $src[$remote];
		}
		
	}
	
	public function isComplete() {
		$fields = array_keys($this->fields);
		foreach ($fields as $field)
			if (!isset ($this->$field)) return false;
	}
	
	public function getDBRecord(databaseRecord$record) {
		$fields = array_keys($this->fields);
		foreach ($fields as $field) $record->{$field} = $this->$field;
		return $record;
	}
	
	/**
	 * Check if the contents of a field are valid.
	 * 
	 * @param string $field
	 * @param mixed $value
	 * @return boolean
	 */
	public function _validate($field, $value) {
		
		$fnname = 'validate' . ucfirst($field);
		
		if (method_exists($this, $fnname)) {
			$callback = Array($this, $fnname);
			return call_user_func_array($callback, (array)$value);
		}
		#If there is no way to validate guess it's ok
		return true;
	}

	
	/**
	 * Returns an instance of a required bean.
	 * 
	 * @param type $name The classname of the bean without Bean at the end of
	 *                   the string.
	 * 
	 * @return CoffeeBean
	 */
	public static function getBean($name) {
		#Create a camel cased string for the class
		$class_name = ucfirst($name) . 'Bean';
		
		#Check if it exists and instance
		if (class_exists($class_name)) {
			return new $class_name();
		}
		else throw new privateException('Bean not found');
	}
	
}