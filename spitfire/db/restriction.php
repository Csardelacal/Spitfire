<?php namespace spitfire\storage\database;

use Model;
use spitfire\exceptions\PrivateException;

abstract class Restriction
{
	private $query;
	private $field;
	private $value;
	private $operator;
	
	private $joins;

	const LIKE_OPERATOR  = 'LIKE';
	const EQUAL_OPERATOR = '=';
	
	public function __construct($query, $field, $value, $operator = '=') {
		if (is_null($operator)) $operator = self::EQUAL_OPERATOR;
		
		if (!$query instanceof Query && $query !== null)
			throw new PrivateException("Valid field or null required");
		
		if ($value instanceof Model)
			$value = $value->getQuery();
		
		if (!$field instanceof QueryField)
			throw new PrivateException("Invalid field");
		
		$this->query    = $query;
		$this->field    = $field;
		$this->value    = $value;
		$this->operator = trim($operator);
	}
	
	public function getTable(){
		return $this->field->getField()->getTable();
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
	 * @return \spitfire\storage\database\Query
	 */
	public function getQuery() {
		return $this->query;
	}
	
	public function setQuery($query) {
		$this->query = $query;
		$this->field->setQuery($query);
	}
	
	public function getOperator() {
		if (is_array($this->value) && $this->operator != 'IN' && $this->operator != 'NOT IN') return 'IN';
		return $this->operator;
	}

	public function getValue() {
		return $this->value;
	}
	
	
	public function getPhysicalSubqueries() {
		return Array();
	}
	
	public function getConnectingRestrictions() {
		return Array();
	}
	
	abstract public function __toString();
}
