<?php

use spitfire\model\Field;

class Model
{
	
	protected $id;
	
	public function __construct() {
		$this->id = new IntegerField(true);
		$this->id->setPrimary(true);
		$this->id->setAutoIncrement(true);
	}
	
	public function getFields() {
		$fields = array_filter(get_object_vars($this));
		#If the given type os a field return it.
		foreach($fields as $name => $field) 
			if (!$field instanceof Field) unset($fields[$name]);
		return $fields;
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
		$ref = new $modelname();
		$fields = $ref->getPrimary();
		
		foreach ($fields as $name => $type) {
			$this->{"{$model}_{$name}"} = clone $type;
			$this->{"{$model}_{$name}"}->setPrimary(false);
			$this->{"{$model}_{$name}"}->setAutoIncrement(false);
			$this->{"{$model}_{$name}"}->setReference($ref, $name);
		}
	}
	
}
