<?php

use spitfire\model\Field;

class FileField extends Field
{
	public function getDataType() {
		return Field::TYPE_FILE;
	}	
}