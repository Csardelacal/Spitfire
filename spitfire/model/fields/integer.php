<?php

use spitfire\model\Field;
use spitfire\validation\ValidationError;

class IntegerField extends Field
{
	
	protected $unsigned;
	
	
	public function __construct( $unsigned = false) {
		$this->datatype = Field::TYPE_INTEGER;
		$this->unsigned = $unsigned;
	}
	
	public function isUnsigned() {
		return $this->unsigned;
	}

	public function getDataType() {
		return Field::TYPE_INTEGER;
	}
	
	public function validate($value) {
		if (!is_numeric($value)) { return new ValidationError(_t('err_not_numeric', $this->length)); }
		else { return parent::validate($value); }
	}
	
}
