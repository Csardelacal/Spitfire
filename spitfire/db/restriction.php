<?php

namespace spitfire\storage\database;

class Restriction
{
	private $field;
	private $value;
	private $operator;
	
	private $stringifyCallback;

	const LIKE_OPERATOR  = 'LIKE';
	const EQUAL_OPERATOR = '=';
	
	public function __construct(Field$field, $value, $operator = '=') {
		if (is_null($operator)) $operator = self::EQUAL_OPERATOR;
		$this->field    = $field;
		$this->value    = $value;
		$this->operator = trim($operator);
	}
	
	public function getTable(){
		return $this->field->getTable();
	}
	
	public function setTable() {
		throw new privateException('Deprecated');
	}
	
	public function getField() {
		return $this->field;
	}
	
	public function getRID() {
		return $this->rid;
	}
	
	public function getOperator() {
		if (is_array($this->value) && $this->operator != 'IN' && $this->operator != 'NOT IN') return 'IN';
		return $this->operator;
	}

	public function getValue() {
		return $this->value;
	}
	
	public function getQueryStr($pattern = ':f :o ?') {
		
		$search  = Array(':f', ':o', ':r', ':v');
		$replace = Array(&$this->field, &$this->operator, &$this->rid, &$this->value);
		
		return str_replace($search, $replace, $pattern);
	}
	
	public function setStringify($callback) {
		$this->stringifyCallback = $callback;
	}
	
	public function __toString() {
		
		if ( $this->stringifyCallback ) {
			$param_arr = Array($this);
			return (string)call_user_func_array ($this->stringifyCallback, $param_arr);
		}
		
		return "$this->field $this->operator ?";
	}
}