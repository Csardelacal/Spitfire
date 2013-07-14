<?php

namespace spitfire\storage\database;

abstract class Restriction
{
	private $query;
	private $field;
	private $value;
	private $operator;

	const LIKE_OPERATOR  = 'LIKE';
	const EQUAL_OPERATOR = '=';
	
	public function __construct($query, $field, $value, $operator = '=') {
		if (is_null($operator)) $operator = self::EQUAL_OPERATOR;
		
		if (!$query instanceof Query && $query !== null)
			throw new \privateException("Valid field or null required");
		
		$this->query    = $query;
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
	
	/**
	 * Returns the query this restriction belongs to. This allows a query to 
	 * define an alias for the table in order to avoid collissions.
	 * 
	 * @return spitfire\storage\database\Query
	 */
	public function getQuery() {
		return $this->query;
	}
	
	public function getOperator() {
		if (is_array($this->value) && $this->operator != 'IN' && $this->operator != 'NOT IN') return 'IN';
		return $this->operator;
	}

	public function getValue() {
		return $this->value;
	}
	
	abstract public function __toString();
}