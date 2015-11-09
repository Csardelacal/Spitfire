<?php

use spitfire\model\Field;
use spitfire\Model;
use spitfire\validation\ValidationError;
use spitfire\model\adapters\StringAdapter;

class StringField extends Field
{
	
	protected $length;
	
	public function __construct($length) {
		$this->datatype = Field::TYPE_STRING;
		$this->length   = $length;
	}
	
	public function getLength() {
		return $this->length;
	}

	public function getDataType() {
		return Field::TYPE_STRING;
	}
	
	public function validate($value) {
		if (strlen($value) > $this->length) { return new ValidationError(_t('str_too_long', $this->length)); }
		else { return parent::validate($value); }
	}

	public function getAdapter(Model $model) {
		return new StringAdapter($this, $model);
	}

	public function getConnectorQueries(\spitfire\storage\database\Query $parent) {
		return Array();
	}

}