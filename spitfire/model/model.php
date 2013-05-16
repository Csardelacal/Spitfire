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
	
	public function getReferencedFields(Model$target = null, $alias = null) {
		#Init the fields array to be returned
		$fields = Array();
		
		#If a target model is set get the related fields
		if (is_null($target)) $references = $this->references;
		elseif (in_array($target, $this->references)) $references = Array($alias => $target);
		else throw new BadMethodCallException('No valid model specified');
		
		#Get the fields for the target model(s)
		foreach ($references as $alias => $reference) {
			$primary = $reference->getPrimary();
			foreach($primary as $field) {
				$ref   = $field;
				$field = clone $field;
				$name = $alias . '_' . $field->getName();
				$field->setName($name);
				$field->setPrimary($reference->primary());
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
		$fields = $this->fields;
		
		foreach($fields as $name => $content) {
			if (!$content->isPrimary()) unset($fields[$name]);
		}
		
		return $fields;
	}
	
	public function getName() {
		static $name;
		if ($name) return $name;
		
		$name = get_class($this);
		$name = substr($name, 0, 0 - strlen('Model'));
		return strtolower($name);
	}
	
	public function getTableName() {
		return str_replace('\\', '-', $this->getName());
	}
	
	public function getBaseRestrictions() {
		return Array();
	}

	public function reference($model, $alias = null) {
		$modelname = $model.'Model';
		if (is_null($alias)) $alias = $model;
		
		if (get_class($this) == $modelname) $this->references[$alias] = $this;
		else $this->references[$alias] = new $modelname();
		
		return $this->references[$alias];
	}
	
	public function unreference($model) {
		unset($this->references[$model]);
	}
	
	/**
	 * Adds a new field to the model.
	 * 
	 * @param string $name
	 * @param string $instanceof
	 * @param int $length
	 * @return Field
	 */
	public function field($name, $instanceof, $length = false) {
		return $this->fields[$name] = new $instanceof($name, $length);
	}
	
	public function primary($set = null) {
		static $primary = false;
		
		if (!is_null($set)) $primary = $set;
		else return $primary;
	}
	
}
