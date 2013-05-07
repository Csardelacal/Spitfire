<?php

use spitfire\model\Field;

class FileField extends Field
{
	
	public function __construct($name) {
		$this->datatype = Field::TYPE_FILE;
		$this->name = $name;
	}
}