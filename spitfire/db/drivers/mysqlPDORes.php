<?php

class _SF_mysqlPDOResultSet implements resultSetInterface
{
	private $result;
	private $table;
	
	public function __construct($table, $stt) {
		$this->result = $stt;
		$this->table = $table;
	}

	public function fetch() {
		$data = $this->result->fetch(PDO::FETCH_ASSOC);
		return new databaseRecord($this->table, $data);
	}

	public function fetchAll() {
		$data = $this->result->fetchAll(PDO::FETCH_ASSOC);
		foreach($data as &$el) $el = new databaseRecord($this->table, $el);
		return $data;
	}
}