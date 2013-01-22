<?php

class _SF_DBQuery
{
	protected $result;
	/** @var _SF_DBTable  */
	protected $table;
	
	protected $restrictions;
	protected $restrictionGroups;
	protected $page = 1;
	protected $rpp = 20;
	protected $order;
	
	public function __construct($table) {
		$this->table = $table;
		$this->restrictions = Array();
	}
	
	public function addRestriction($restriction) {
		$this->restrictions[] = $restriction;
		$this->result = false;
		return $this;
	}
	
	public function group() {
		return $this->restrictionGroups[] = new _SF_RestrictionGroup($this);
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
			$values[] = new _SF_RestrictionGroup($this, $this->restrictions);
		}
		
		return $values;
	}
	
	public function getOrder() {
		return $this->order;
	}
}
