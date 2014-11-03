<?php

namespace spitfire\storage\database;

abstract class QueryField
{
	private $field;
	private $query;
	
	public function __construct(Query$query, $field) {
		$this->query = $query;
		$this->field = $field;
	}
	
	public function setQuery($query) {
		$this->query = $query;
	}
	
	public function getQuery() {
		return $this->query;
	}
	
	public function getField() {
		return $this->field;
	}
	
	public function isLogical() {
		return $this->field instanceof \spitfire\model\Field;
	}
	
	public function getPhysical() {
		if ($this->isLogical()) {
			$fields = $this->field->getPhysical();
			foreach ($fields as &$field) $field = $this->query->queryFieldInstance($field);
			unset($field);
			return $fields;
		}
	}
	
	abstract public function __toString();
}