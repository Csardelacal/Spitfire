<?php

use spitfire\model\Field;

class DatetimeField extends Field
{

	public function getDataType() {
		return Field::TYPE_DATETIME;
	}
	
}