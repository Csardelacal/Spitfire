<?php

use spitfire\model\Field;
use spitfire\Model;
use spitfire\model\adapters\EnumAdapter;

class EnumField extends Field
{
	private $options;
	
	public function __construct() {
		$this->options = func_get_args();
	}
	
	public function getOptions() {
		return $this->options;
	}
	
	public function getLength() {
		return 20;
	}
	
	public function getDataType() {
		return Field::TYPE_STRING;
	}

	public function getAdapter(Model $model) {
		return new EnumAdapter($this, $model);
	}

	public function getConnectorQueries(\spitfire\storage\database\Query $parent) {
		return Array();
	}

}