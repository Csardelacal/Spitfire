<?php

use spitfire\io\html\HTMLForm;
use spitfire\io\html\HTMLTable;
use spitfire\io\html\HTMLTableRow;

abstract class CoffeeBean
{
	const METHOD_POST = 'POST';
	const METHOD_GET  = 'GET';
	
	const VISIBILITY_HIDDEN = 0;
	const VISIBILITY_LIST   = 1;
	const VISIBILITY_FORM   = 2;
	const VISIBILITY_ALL    = 3;
	
	private $fields = Array();
	public $name;
	public $model;
	
	
	public function makeDBRecord() {
		if ($this->model) {
			$fields = $this->fields;
			$record = db()->table($this->model)->newRecord();
			foreach ($fields as $field) $record->{$field->getModelField()} = $field->getValue();
		}
		return $record;
	}
	
	public function updateDBRecord(databaseRecord$record) {
		if ($this->model) {
			$fields = $this->fields;
			foreach ($fields as $field) $record->{$field->getModelField()} = $field->getValue();
		}
		return $record;
	}
	
	public function setDBRecord(databaseRecord$record) {
		if ($this->model) {
			$fields = $this->fields;
			foreach ($fields as $field) $field->setValue($record->{$field->getModelField()});
		}
		return $record;
	}
	
	/**
	 * Creates a new field for the bean.
	 * 
	 * @param string $instanceof
	 * @param string $name
	 * @param string $caption
	 * @param string $method
	 * @return spitfire\io\beans\Field
	 */
	public function field($instanceof, $name, $caption, $method = CoffeeBean::METHOD_POST) {
		$instanceof = "\\spitfire\\io\\beans\\$instanceof";
		return $this->fields[] = new $instanceof($name, $caption, $method);
	}
	
	public function getFields() {
		return $this->fields;
	}
	
	public function getName() {
		if ($this->name) return $this->name;
		else return get_class ($this);
	}


	public function makeForm($action) {
		return new HTMLForm($action, $this);
	}
	
	/**
	 * Creates a list of records according to this Bean's settings.
	 * 
	 * @todo Implement actions
	 * @param type $records
	 * @param type $actions
	 * @return \spitfire\io\html\HTMLTable
	 */
	public function makeList($records, $actions = Array()) {
		$table = new HTMLTable();
		//headers
		$row = new HTMLTableRow();
		foreach ($this->fields as $field) {
			if ($field->getVisibility() == CoffeeBean::VISIBILITY_ALL || $field->getVisibility() == CoffeeBean::VISIBILITY_LIST)
			$row->putCell($field->getCaption());
		}
		$row->putCell('Actions');
		$table->putRow($row);
		//Content
		foreach($records as $record) {
			$row = new HTMLTableRow();
			foreach ($this->fields as $field) {
				if ($field->getVisibility() == CoffeeBean::VISIBILITY_ALL || $field->getVisibility() == CoffeeBean::VISIBILITY_LIST)
				$row->putCell($record->{$field->getModelField()});
			}
			//Actions
			$str = '';
			foreach ($actions as $name => $url) {
				$action = sprintf($url, $record->id);
				$str.= sprintf('<a href="%s">%s</a>', $action, $name);
			}
			$row->putCell($str);
			
			$table->putRow($row);
		}
		return $table;
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