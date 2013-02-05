<?php

use spitfire\model\Field;

class DatetimeField extends Field
{
	
	public function __construct($name) {
		$this->name = $name;
		$this->datatype = Field::TYPE_DATETIME;
	}
	
}