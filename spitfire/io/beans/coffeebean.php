<?php

use spitfire\io\beans\ChildBean;
use spitfire\io\html\HTMLForm;

abstract class CoffeeBean extends Validatable
{
	
	const VISIBILITY_HIDDEN = 0;
	const VISIBILITY_LIST   = 1;
	const VISIBILITY_FORM   = 2;
	const VISIBILITY_ALL    = 3;
	
	const STATUS_SUBMITTED_OK  = 2;
	const STATUS_SUBMITTED_ERR = 1;
	const STATUS_UNSUBMITTED   = 0;
	
	private $fields = Array();
	private $record;
	
	public $name;
	public $model;
	
	/**
	 * This function informs you about the status of the bean. This status
	 * can take three different values.
	 * <ul>
	 * <li>STATUS_SUBMITTED_OK: If the bean did receive data and it is valid.</li>
	 * <li>STATUS_SUBMITTED_ERR: If the data was received but not valid</li>
	 * <li>STATUS_UNSUBMITTED: If the data wasn't received at all</li>
	 * </ul>
	 * 
	 * This function is meant to aid you taking the decision whether the bean
	 * should display a form or store the data. To do so you can compare the
	 * values or compare status to be less (&lt;) than OK.
	 * 
	 * @return int Status code of the submission
	 */
	public function getStatus() {
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if ($this->validate())
				return self::STATUS_SUBMITTED_OK;
			else
				return self::STATUS_SUBMITTED_ERR;
		}
		else {
			return self::STATUS_UNSUBMITTED;
		}
	}
	
	public function updateDBRecord() {
		
		$record = $this->record;
		
		if ($this->model) {
			$fields = $this->fields;
			foreach ($fields as $field) {
				if ($field->getValue())
					$record->{$field->getModelField()} = $field->getValue();
			}
		}
	}
	
	public function setDBRecord(databaseRecord$record) {
		$this->record = $record;
	}
	
	public function getRecord() {
		return $this->record;
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
	public function field($instanceof, $name, $caption) {
		$instanceof = "\\spitfire\\io\\beans\\$instanceof";
		return $this->fields[$name] = new $instanceof($this, $name, $caption);
	}
	
	public function childBean($beanname) {
		return $this->fields[$beanname] = new ChildBean($this, $beanname, $beanname);
	}
	
	public function getFields() {
		return $this->fields;
	}
	
	public function getField($name) {
		return $this->fields[$name];
	}
	
	public function getName() {
		if ($this->name) return $this->name;
		else return substr( get_class ($this), 0, - strlen('Bean'));
	}


	public function makeForm($renderer) {
		return $renderer->renderForm($this);
	}
	
	
	public function makeList($renderer, $records) {
		return $renderer->renderList($this, $records);
	}
	
	/**
	 * Check if the contents of this bean are valid.
	 * 
	 * @param mixed $data Is ignored
	 * @return boolean
	 */
	public function validate($data = null) {
		foreach ($this->fields as $field => $content) $data[$field] = $content->getValue(); 
		return parent::validate($data);
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