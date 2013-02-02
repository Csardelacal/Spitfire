<?php

use spitfire\model\Field;

class TextField extends Field
{
	
	public function __construct($name) {
		$this->name = $name;
		$this->datatype = Field::TYPE_TEXT;
	}
	
}