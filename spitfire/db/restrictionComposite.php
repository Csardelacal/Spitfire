<?php

namespace spitfire\storage\database;

use spitfire\model\Field;

class CompositeRestriction
{
	/**
	 * The parent query for this restriction. This provides information about
	 * how it's table is currently aliased and what fields this table can provide.
	 *
	 * @var spitfire\storage\database\Query
	 */
	private $query;
	private $field;
	private $value;
	private $operator;
	
	public function __construct(Query$query, Field$field, $value, $operator = Restriction::EQUAL_OPERATOR) {
		$this->query = $query;
		$this->field = $field;
		$this->value = $value;
		$this->operator = $operator;
	}
	
	/**
	 * 
	 * @return spitfire\storage\database\Query
	 */
	public function getQuery() {
		return $this->query;
	}

	public function setQuery(Query$query) {
		$this->query = $query;
	}

	public function getField() {
		return $this->field;
	}

	public function setField(Field$field) {
		$this->field = $field;
	}

	public function getValue() {
		return $this->value;
	}

	public function setValue($value) {
		$this->value = $value;
	}

	public function getOperator() {
		return $this->operator;
	}

	public function setOperator($operator) {
		$this->operator = $operator;
	}
	
	public function getSimpleRestrictions() {
		if ($this->value instanceof Model) 
			$this->value = $this->value->getQuery();
		
		if ($this->value instanceof Query)
			return $this->value->getRestrictions();
	}
	
	public function getConnectingRestrictions() {
		$physical     = $this->field->getPhysical();
		$restrictions = Array();
		
		foreach($physical as $field) {
			$this->getQuery()->restrictionInstance(
					  $this->query->queryFieldInstance($field), 
					  $this->value->queryFieldInstance($field->getReferencedField()), 
					  $this->operator);
		}
		
		return $restrictions;
	}

}