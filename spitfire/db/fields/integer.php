<?php

use spitfire\storage\database\Field;

class IntegerField extends Field
{
	
	protected $unsigned;
	
	
	public function __construct($unsigned = false) {
		$this->datatype = Field::TYPE_INTEGER;
		$this->unsigned = $unsigned;
	}
	
	public function isUnsigned() {
		return $this->unsigned;
	}
	
}
