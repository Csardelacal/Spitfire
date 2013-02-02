<?php

use spitfire\model\Field;

class StringField extends Field
{
	
	protected $length;
	
	public function __construct($name, $length) {
		$this->datatype = Field::TYPE_STRING;
		$this->length   = $length;
		$this->name = $name;
	}
	
	public function getLength() {
		return $this->length;
	}
}