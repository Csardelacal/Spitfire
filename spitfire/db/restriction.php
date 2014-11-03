<?php

namespace spitfire\storage\database;

use Model;

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
			throw new \privateException("Valid field or null required");
		
		if ($value instanceof Model)
			$value = $value->getQuery();
		
		if (!$field instanceof QueryField)
			throw new \privateException("Invalid field");
		
		$this->query    = $query;
		$this->field    = $field;
		$this->value    = $value;
		$this->operator = trim($operator);
	}
	
	/**
	 * @deprecated since 0.1dev
	 * @throws \privateException
	 */
	public function makeJoins() {
		
		$_joins = Array();
		
		if ($this->value instanceof Query) {
			$this->value->setAliased(true);
		}
		
		if ($this->field->getField() instanceof \Reference && $this->field->getField()->getTable() === $this->getQuery()->getTable()) {
			$_joins[] = $this->queryJoin($this->value, $this->query, $this->field, QueryJoin::RIGHT_JOIN);
		}
		
		if ($this->field->getField() instanceof \Reference && $this->field->getField()->getTable() !== $this->getQuery()->getTable()) {
			$_joins[] = $this->queryJoin($this->value, $this->query, $this->field);
		}
		
		if ($this->field->getField() instanceof \ChildrenField && !$this->field->getField() instanceof \ManyToManyField && $this->field->getField()->getTable() === $this->getQuery()->getTable()) {
			$_joins[] = $this->queryJoin($this->value, $this->query, $this->field->getField()->getReferencedField());
		}
		
		if ($this->field->getField() instanceof \ManyToManyField && $this->value instanceof Query && $this->field->getField()->getTable() === $this->getQuery()->getTable()) {
			$join = $this->queryJoin($this->value, $this->query, $this->field);
			if (!$this->field->getField()->getBridge() instanceof \Schema) throw new \privateException("$this->field has no valid bridge received a " . get_class($this->field->getBridge()));
			$join->setBridge($this->field->getField()->getBridge()->getTable());
			$_joins[] = $join;
		}
		
		$this->joins = $_joins;
		
	}
	
	/**
	 * 
	 * @deprecated since version 0.1dev
	 */
	public function getJoins() {
		if (!$this->joins) $this->makeJoins();
		
		if ($this->value instanceof Query) return array_merge ($this->joins, $this->value->getJoins());
		else return $this->joins;
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
	 * @return spitfire\storage\database\Query
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
	
	/**
	 * @deprecated since version 0.1dev
	 */
	abstract public function queryJoin($value, $query, $field, $type = null);
	
	abstract public function __toString();
}