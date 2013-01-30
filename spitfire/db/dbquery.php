<?php

namespace spitfire\storage\database;

use databaseRecord;
use privateException;

class Query
{
	/** @var spitfire\storage\database\drivers\ResultSetInterface */
	protected $result;
	/** @var \spitfire\storage\database\Table  */
	protected $table;
	
	protected $restrictions;
	protected $restrictionGroups;
	protected $join;
	protected $page = 1;
	protected $rpp = 20;
	protected $order;
	
	public function __construct($table) {
		$this->table = $table;
		$this->restrictions = Array();
	}
	
	/**
	 * Adds a restriction to the current query. Restraining the data a field
	 * in it can contain.
	 * 
	 * @param string $field
	 * @param mixed  $value
	 * @param string $operator
	 * @return spitfire\storage\database\Query
	 */
	public function addRestriction($fieldname, $value, $operator = '=') {
		$field = $this->table->getField($fieldname);
		if ($field == null) throw new privateException("No field '$fieldname'");
		$restriction = new Restriction($field, $value, $operator);
		$this->restrictions[] = $restriction;
		$this->result = false;
		return $this;
	}
	
	/**
	 * Creates a new set of alternative restrictions for the current query.
	 * 
	 * @return RestrictionGroup
	 */
	public function group() {
		return $this->restrictionGroups[] = new RestrictionGroup($this);
	}
	
	/**
	 * Sets the ammount of results returned by the query.
	 * @param int $amt
	 */
	public function setResultsPerPage($amt) {
		$this->rpp = $amt;
	}
	
	/**
	 * @return int The amount of results the query returns when executed.
	 */
	public function getResultsPerPage() {
		return $this->rpp;
	}
	
	/**
	 * @param int $page The page of results currently displayed.
	 * @return boolean Returns if the page se is valid.
	 */
	public function setPage ($page) {
		#The page can't be lower than 1
		if ($page < 1) return false;
		$this->page = $page;
		return true;
	}
	
	public function getPage() {
		return $this->page;
	}
	
	public function getErrors() {
		return $this->table->getErrors();
	}
	
	public function setOrder ($field, $mode) {
		$this->order['field'] = $field;
		$this->order['mode'] = $mode;
		return $this;
	}
	
	/**
	 * Returns a record from a databse that matches the query we sent.
	 * 
	 * @return databaseRecord
	 */
	public function fetch() {
		if (!$this->result) $this->query();
		$data = $this->result->fetch();
		return  $data;//array_map(Array($this->table->getDB(), 'convertIn'), $data) ;
	}
	
	public function fetchAll() {
		if (!$this->result) $this->query();
		return $this->result->fetchAll();
	}

	protected function query($fields = false, $returnresult = false) {
		$result = $this->table->getDB()->query($this->table, $this, $fields);
		if ($returnresult) return $result;
		else $this->result = $result;
		return $this;
	}
	
	public function count() {
		$query = $this->query(Array('count(*)'), true)->fetch();
		$count = $query->{'count(*)'};//end($query);
		return $count;
	}
	
	public function getRestrictions() {
		$values = $this->restrictionGroups;
		
		if (!empty($this->restrictions)) {
			$values[] = new RestrictionGroup($this, $this->restrictions);
		}
		
		return $values;
	}
	
	public function getOrder() {
		return $this->order;
	}
	
	public function setJoin(databaseRecord$record) {
		$this->join = $record;
	}
	
	public function getJoin() {
		return $this->join;
	}
	
	public function getTable() {
		return $this->table;
	}
}
