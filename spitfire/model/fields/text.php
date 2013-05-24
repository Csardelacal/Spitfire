<?php

use spitfire\model\Field;

class TextField extends Field
{
	
	public function __construct() {
		$this->datatype = Field::TYPE_TEXT;
	}
	
}