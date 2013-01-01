<?php

class _SF_mysqlPDOResultSet implements resultSetInterface
{
	private $result;
	
	public function __construct($stt) {
		$this->result = $stt;
	}

	public function fetch() {
		return $this->result->fetch(PDO::FETCH_ASSOC);
	}

	public function fetchAll() {
		return $this->result->fetchAll(PDO::FETCH_ASSOC);
	}
}