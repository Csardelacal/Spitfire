<?php

use spitfire\model\Field;
use spitfire\model\adapters\IntegerAdapter;
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

	public function getAdapter(\Model $model) {
		return new IntegerAdapter($this, $model);
	}

	public function getConnectorQueries(\spitfire\storage\database\Query $parent) {
		return Array();
	}

}
