<?php

use spitfire\model\Field;

class TextField extends Field
{
	
	public function getDataType() {
		return Field::TYPE_TEXT;
	}
	
}