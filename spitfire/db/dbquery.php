<?php

namespace spitfire\storage\database;

use Model;
use privateException;

abstract class Query
{
	/** @var spitfire\storage\database\drivers\ResultSetInterface */
	protected $result;
	/** @var \spitfire\storage\database\Table  */
	protected $table;
	
	protected $restrictions;
	protected $restrictionGroups;
	protected $page = 1;
	protected $rpp = -1;
	protected $order;
	
	private static $counter = 1;
	private $id;
	private $aliased = false;


	public function __construct($table) {
		$this->id = self::$counter++;
		$this->table = $this->queryTableInstance($table);
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
		try {
			$field = $this->table->getField($fieldname);
		} catch (\Exception $e) {
			$field = $this->table->getModel()->getField($fieldname);
		}
		if ($field == null) {
			if ($fieldname instanceof \Reference && $fieldname->getTarget() === $this->table->getModel()) {
				$field = $fieldname;
			}
			else {
				throw new privateException("No field '$fieldname'");
			}
		}
		$restriction = $this->restrictionInstance($this->queryFieldInstance($field), $value, $operator);
		$this->restrictions[] = $restriction;
		$this->result = false;
		return $this;
	}
	
	public function setAliased($aliased) {
		$this->aliased = $aliased;
	}
	
	public function getAliased() {
		return $this->aliased;
	}
	
	public function getAlias() {
		if ($this->aliased)
			return $this->table->getTablename() . '_' . $this->id;
		else
			return $this->table->getTablename();
	}
	
	public function getJoins() {
		$_joins = Array();
		
		foreach($this->restrictions as $restriction) {
			$_joins = array_merge($_joins, $restriction->getJoins());
		}
		
		return $_joins;
	}
	
	/**
	 * Creates a new set of alternative restrictions for the current query.
	 * 
	 * @return RestrictionGroup
	 */
	public function group() {
		return $this->restrictions[] = $this->restrictionGroupInstance($this);
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
	 * @return Model
	 */
	public function fetch() {
		if (!$this->result) $this->query();
		$data = $this->result->fetch();
		return  $data;//array_map(Array($this->table->getDB(), 'convertIn'), $data) ;
	}
	
	public function fetchAll($parent = null) {
		if (!$this->result) $this->query();
		return $this->result->fetchAll($parent);
	}

	protected function query($fields = null, $returnresult = false) {
		$result = $this->execute($fields);
		if ($returnresult) return $result;
		else $this->result = $result;
		return $this;
	}
	
	public function count() {
		$query = $this->query(Array('count(*)'), true)->fetchArray();
		$count = $query['count(*)'];//end($query);
		return $count;
	}
	
	public function getRestrictions() {
		$this->table->getModel()->getBaseRestrictions($this);
		return $this->restrictions;
	}
	
	public function getOrder() {
		return $this->order;
	}
	
	public function getTable() {
		return $this->table;
	}
	
	public function __toString() {
		return $this->getTable() . implode(',', $this->getRestrictions());
	}
	
	public abstract function execute($fields = null);
	public abstract function restrictionInstance(QueryField$field, $value, $operator);
	public abstract function restrictionGroupInstance();
	public abstract function queryFieldInstance(Field$field);
	public abstract function queryTableInstance(Table$table);
	
	/**
	 * @deprecated since version 0.1
	 */
	public abstract function aliasedTableName();
}
