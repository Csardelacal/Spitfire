<?php

use spitfire\model\Field;

class FileField extends Field
{
	
	public function __construct() {
		$this->datatype = Field::TYPE_FILE;
	}
}