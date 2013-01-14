<?php

/**
 * This class allows to track changes on database data along the use of a program
 * and creates interactions with the database in a safe way.
 */
class databaseRecord
{
	
	private $src;
	private $data;
	private $table;
	
	public function __construct(_SF_DBTable $table, $srcData = Array() ) {
		$this->src   = $srcData;
		$this->data  = $srcData;
		$this->table = $table;
	}
	
	public function setData($newdata) {
		$this->data = $newdata;
	}
	
	public function getData() {
		return $this->data;
	}
	
	public function getSrcData() {
		return $this->src;
	}
	
	public function getDiff() {
		$changed = Array();
		
		foreach($this->data as $key => $value) {
			if ($value != $this->src[$key]) $changed[$key] = $value;
		}
		
		return $changed;
	}
	
	public function store() {
		//TODO!!!
		//Needs to select wether it should insert
		//Update
		//Or Increment
	}

	public function __set($field, $value) {
		if (in_array($field, $this->table->getFields())) {
			$this->data[$field] = $value;
		}
		else throw new privateException ('Setting non-existent database field: ' . $field);

	}
	
	public function __get($field) {
		return $this->data[$field];
	}
	
}