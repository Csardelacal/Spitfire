<?php

use spitfire\storage\database\Field;

class StringField extends Field
{
	
	protected $length;
	
	public function __construct($length) {
		$this->datatype = Field::TYPE_STRING;
		$this->length   = $length;
	}
	
	public function getLength() {
		return $this->length;
	}
}