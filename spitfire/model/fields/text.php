<?php

use spitfire\model\Field;
use spitfire\model\adapters\StringAdapter;

class TextField extends Field
{
	
	public function getDataType() {
		return Field::TYPE_TEXT;
	}
	
	public function getAdapter(\Model $model) {
		return new StringAdapter($this, $model);
	}
}