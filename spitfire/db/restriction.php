<?php

class _SF_Restriction
{
	private $field;
	private $value;
	private $operator;
	private $rid;
	
	public static $autonumeric = 0;
	
	const LIKE_OPERATOR  = 'LIKE';
	const EQUAL_OPERATOR = '=';
	
	public function __construct($field, $value, $operator = '=') {
		$this->field    = $field;
		$this->value    = $value;
		$this->operator = $operator;
		//To avoid restriction colliding
		$this->rid      = $field . self::$autonumeric;
		self::$autonumeric++;
	}
	
	public function getField() {
		return $this->field;
	}
	
	public function getRID() {
		return $this->rid;
	}

	public function getValue() {
		return $this->value;
	}
	
	public function getQueryStr($pattern = ':f :o ?') {
		
		$search  = Array(':f', ':o', ':r', ':v');
		$replace = Array(&$this->field, &$this->operator, &$this->rid, &$this->value);
		
		return str_replace($search, $replace, $pattern);
	}
	
	public function __toString() {
		return "`$this->field` $this->operator ?";
	}
}