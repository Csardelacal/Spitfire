<?php

use spitfire\model\Field;

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
	
}