<?php

class _SF_Restriction
{
	private $field;
	private $value;
	private $operator;
	
	const LIKE_OPERATOR  = 'LIKE';
	const EQUAL_OPERATOR = '=';
	
	public function __construct($field, $value, $operator = '=') {
		$this->field    = $field;
		$this->value    = $value;
		$this->operator = $operator;
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