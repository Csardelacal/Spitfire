<?php

use spitfire\model\Field;
use spitfire\Model;
use spitfire\model\adapters\DateTimeAdapter;

class DatetimeField extends Field
{

	public function getDataType() {
		return Field::TYPE_DATETIME;
	}

	public function getAdapter(Model $model) {
		return new DateTimeAdapter($this, $model);
	}

	public function getConnectorQueries(\spitfire\storage\database\Query $parent) {
		return Array();
	}

}