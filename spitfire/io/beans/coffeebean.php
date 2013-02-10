<?php

use spitfire\io\html\HTMLForm;

abstract class CoffeeBean
{
	const METHOD_POST = 'POST';
	const METHOD_GET  = 'GET';
	
	private $fields = Array();
	public $model;
	
	public function insertIntoDBRecord(databaseRecord$record) {
		throw new Exception('coffebean::insertIntoDBRecord - Not implemented');
	}
	
	public function field($instanceof, $name, $caption, $method = CoffeeBean::METHOD_POST) {
		$instanceof = "\\spitfire\\io\\beans\\$instanceof";
		return $this->fields[] = new $instanceof($name, $caption, $method);
	}
	
	public function getFields() {
		return $this->fields;
	}
	
	public function makeForm($action) {
		return new HTMLForm($action, $this);
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