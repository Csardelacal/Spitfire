<?php

class _SF_Restriction
{
	private $field;
	private $value;
	private $operator = '=';
	
	public function __construct($field, $value) {
		$this->field = $field;
		$this->value = $value;
	}
	
	public function getField() {
		return $this->field;
	}
	
	public function getValue() {
		return $this->value;
	}
	
	public function __toString() {
		return "$this->field = :$this->field";
	}
}