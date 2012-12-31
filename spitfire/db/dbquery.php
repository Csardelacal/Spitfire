<?php

class _SF_DBQuery
{
	protected $result;
	/** @var _SF_DBTable  */
	protected $table;
	
	protected $restrictions;
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
		$data = $this->result->fetch(PDO::FETCH_ASSOC);
		return  array_map(Array($this->table, 'convertIn'), $data) ;
	}
	
	public function fetchAll() {
		if (!$this->result) $this->query();
		return $this->result->fetchAll(PDO::FETCH_ASSOC);
	}

	protected function query($fields = false, $returnresult = false) {
		
		$offset = ($this->page - 1) * $this->rpp;
		$rpp    = $this->rpp;
		
		if (!$fields) $fields = $this->table->getFields();
		
		$restrictions = implode(' AND ', $this->restrictions);
		if (empty($restrictions)) $restrictions = '1';//If no restrictions are set fetch everything

		$statement = "SELECT " . 
				implode($fields, ', ') . 
				" FROM `{$this->table->getTablename()}` WHERE  " . 
				$restrictions;
				
		if (!empty($this->order)) {
			$statement.= " ORDER BY ";
			$statement.= $this->order['field'] . ' ' . $this->order['mode'];
		}
		
		if ($this->rpp > 0) $statement.= " LIMIT $offset, $rpp";

		$con = $this->table->getDb()->getConnection();
		$stt = $con->prepare($statement);
		
		$values = Array(); //Prepare the statement to be executed
		foreach($this->restrictions as $r) $values[$r->getRID()] = $r->getValue();
		$stt->execute( array_map(Array($this->table, 'convertOut'), $values) );
		
		$err = $stt->errorInfo();
		if ($err[1]) throw new privateException($err[2] . ' in query ' . $statement, $err[1]);
		
		if ($returnresult) return $stt;
		else $this->result = $stt;

	}
	
	public function count() {
		$query = $this->query(Array('count(*)'), true)->fetch();
		$count = end($query);
		return $count;
	}
}
