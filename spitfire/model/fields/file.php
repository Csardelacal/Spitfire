<?php

use spitfire\model\Field;
use spitfire\Model;
use spitfire\model\adapters\StringAdapter;

class FileField extends Field
{
	public function getDataType() {
		return Field::TYPE_FILE;
	}

	public function getAdapter(Model $model) {
		return new StringAdapter($this, $model);
	}

	public function getConnectorQueries(\spitfire\storage\database\Query $parent) {
		return Array();
	}

}