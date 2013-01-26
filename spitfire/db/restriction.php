<?php

class _SF_Restriction
{
	private $field;
	private $value;
	private $operator;
	private $rid;
	private $table;
	
	private $stringifyCallback;
	
	public static $autonumeric = 0;

	const LIKE_OPERATOR  = 'LIKE';
	const EQUAL_OPERATOR = '=';
	
	public function __construct(_SF_DBField$field, $value, $operator = '=') {
		$this->field    = $field;
		$this->value    = $value;
		$this->operator = $operator;
		//To avoid restriction colliding
		$this->rid      = $field . self::$autonumeric;
		self::$autonumeric++;
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
			return call_user_func_array ($this->stringifyCallback, $param_arr);
		}
		
		//TODO: Clean code
		$tablename = ($this->table)? $this->table->getTableName() . '.' : '';
		
		return "$this->field $this->operator ?";
	}
}