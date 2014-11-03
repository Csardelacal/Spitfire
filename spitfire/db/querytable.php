<?php

namespace spitfire\storage\database;

abstract class QueryTable
{
	private $table;
	private $query;
	
	public function __construct(Query$query, Table$table) {
		$this->query = $query;
		$this->table = $table;
	}
	
	/**
	 * 
	 * @return \spitfire\storage\database\Query
	 */
	public function getQuery() {
		return $this->query;
	}
	
	public function setQuery($query) {
		$this->query = $query;
	}
	
	/**
	 * 
	 * @return \spitfire\storage\database\Table
	 */
	public function getTable() {
		return $this->table;
	}
	
	abstract public function definition();
	abstract public function __toString();
}