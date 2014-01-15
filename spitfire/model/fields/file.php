<?php

use spitfire\model\Field;
use spitfire\model\adapters\StringAdapter;

class FileField extends Field
{
	public function getDataType() {
		return Field::TYPE_FILE;
	}

	public function getAdapter(\Model $model) {
		return new StringAdapter($this, $model);
	}

}