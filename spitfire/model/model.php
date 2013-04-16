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
	
	public function setFields($fields) {
		$this->fields = $fields;
	}
	
	public function getFields() {
		$fields = array_filter(array_merge($this->fields, $this->getReferencedFields()));
		#If the given type os a field return it.
		foreach($fields as $name => $field) 
			if (!$field instanceof Field) unset($fields[$name]);
		return $fields;
	}
        
        public function getField($name) {
            if (isset($this->fields[$name]))     return $this->fields[$name];
            if (isset($this->references[$name])) return $this->references[$name];
            else return null;
        }
	
	public function getReferencedFields(Model$target = null) {
		#Init the fields array to be returned
		$fields = Array();
		
		#If a target model is set get the related fields
		if (is_null($target)) $references = $this->references;
		elseif (in_array($target, $this->references)) $references = Array($target);
		else throw new BadMethodCallException('No valid model specified');
		
		#Get the fields for the target model(s)
		foreach ($references as $reference) {
			$primary = $reference->getPrimary();
			foreach($primary as $field) {
				$ref   = $field;
				$field = clone $field;
				$name = $reference->getName() . '_' . $field->getName();
				$field->setName($name);
				$field->setPrimary(false);
				$field->setAutoIncrement(false);
				$field->setReference($reference, $ref);
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
		$this->references[$model] = new $modelname();
	}
	
	public function field($name, $instanceof, $length = false) {
		return $this->fields[$name] = new $instanceof($name, $length);
	}
	
}
