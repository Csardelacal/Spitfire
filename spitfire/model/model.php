<?php

use spitfire\model\Field;

class Model
{
	private $fields;
	private $references = Array();
	
	public function __construct() {
		$this->field('id', 'IntegerField')
			->setPrimary(true)
			->setAutoIncrement(true);
	}
	
	public function getFields() {
		$fields = array_filter(array_merge($this->fields, $this->getReferencedFields()));
		#If the given type os a field return it.
		foreach($fields as $name => $field) 
			if (!$field instanceof Field) unset($fields[$name]);
		return $fields;
	}
	
	public function getReferencedFields() {
		$fields = Array();
		foreach ($this->references as $reference) {
			$primary = $reference->getPrimary();
			foreach($primary as $field) {
				$field = clone $field;
				$name = $reference->getName() . '_' . $field->getName();
				$field->setName($name);
				$field->setPrimary(false);
				$field->setAutoIncrement(false);
				$fields[$name] = $field;
			}
		}
		return $fields;
	}
	
	public function getReferencedModels() {
		return $this->references;
	}
	
	public function getPrimary() {
		$fields = $this->getFields();
		
		foreach($fields as $name => $content) {
			if (!$content->isPrimary()) unset($fields[$name]);
		}
		
		return $fields;
	}
	
	public function getName() {
		static $name;
		if ($name) return $name;
		return $name = str_replace('Model', '', get_class($this));
	}

	public function reference($model) {
		$modelname = $model.'Model';
		$this->references[] = new $modelname();
	}
	
	public function field($name, $instanceof, $length = false) {
		return $this->fields[$name] = new $instanceof($name, $length);
	}
	
}
