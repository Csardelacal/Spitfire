<?php

class _SF_DBQuery
{
	protected $result;
	protected $table;
	
	protected $restrictions;
	protected $page = 1;
	protected $rpp = 20;
	
	public function __construct($table) {
		$this->table = $table;
		$this->restrictions = Array();
	}
	
	public function addRestriction($restriction) {
		$this->restrictions[] = $restriction;
		$this->result = false;
		return $this;
	}
	
	public function setResultsPerPage ($amt) {
		$this->rpp = $amt;
	}
	
	public function setPage ($page) {
		$this->page = $page;
	}
	
	public function fetch() {
		if (!$this->result) $this->query();
		return $this->result->fetch(PDO::FETCH_ASSOC);
	}

	protected function query() {
		
		$offset = ($this->page - 1) * $this->rpp;
		$rpp    = $this->rpp;

		$statement = "SELECT " . 
				implode($this->table->getFields(), ', ') . 
				" FROM {$this->table->getTablename()} WHERE  " . 
				implode(' AND ', $this->restrictions) . 
				" LIMIT $offset, $rpp";

		$con = $this->table->getDb()->getConnection();
		$stt = $con->prepare($statement);
		
		$values = Array();
		foreach($this->restrictions as $r) $values[$r->getField()] = $r->getValue();
		$stt->execute($values);
		
		$this->result = $stt;

	}
}
