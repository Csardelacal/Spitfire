<?php

namespace spitfire\storage\database;

abstract class QueryField
{
	private $field;
	private $query;
	
	public function __construct(Query$query, DBField$field) {
		$this->query = $query;
		$this->field = $field;
	}
	
	public function getQuery() {
		return $this->query;
	}
	
	public function getField() {
		return $this->field;
	}
	
	abstract public function __toString();
}