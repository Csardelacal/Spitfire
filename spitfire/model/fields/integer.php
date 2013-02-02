<?php

use spitfire\model\Field;

class IntegerField extends Field
{
	
	protected $unsigned;
	
	
	public function __construct($name, $unsigned = false) {
		$this->datatype = Field::TYPE_INTEGER;
		$this->name = $name;
		$this->unsigned = $unsigned;
	}
	
	public function isUnsigned() {
		return $this->unsigned;
	}
	
}
