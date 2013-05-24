<?php

use spitfire\model\Field;

class DatetimeField extends Field
{
	
	public function __construct() {
		$this->datatype = Field::TYPE_DATETIME;
	}
	
}