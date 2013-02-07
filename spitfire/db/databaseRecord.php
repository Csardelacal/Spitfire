<?php

use spitfire\storage\database\Table;
use spitfire\storage\database\Query;
use \spitfire\storage\database\DBField;

/**
 * This class allows to track changes on database data along the use of a program
 * and creates interactions with the database in a safe way.
 * 
 * @package Spitfire.storage.database
 * @author César de la Cal <cesar@magic3w.com>
 */
abstract class databaseRecord
{
	
	private $src;
	private $data;
	private $table;
	
	#Status vars
	private $synced;
	private $deleted;
	
	/**
	 * Creates a new record.
	 * 
	 * @param _SF_DBTable $table DB Table this record belongs to. Easiest way
	 *                       to get this is by using $this->model->*tablename*
	 * 
	 * @param mixed $srcData Attention! This parameter is intended to be 
	 *                       used by the system. To create a new record, leave
	 *                       empty and use setData.
	 */
	public function __construct(Table $table, $srcData = Array() ) {
		$this->src     = $srcData;
		$this->data    = $srcData;
		$this->table   = $table;
		
		$this->synced  = !empty($srcData);
		$this->deleted = false;
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
	
	
	public function isSynced() {
		return $this->synced && !$this->deleted;
	}
	
	public function store() {
		
		if (empty($this->src)) {
			$id = $this->insert();
			$ai = $this->table->getAutoIncrement();
			
			if ($ai && empty($this->data[$ai->getName()]) ) {
				$this->data[$ai->getName()] = $id;
			}
		}
		else {
			$this->update($this);
		}
		
		$this->synced = true;
		$this->src    = $this->data;
	}
	
	public function getUniqueFields() {
		return $this->table->getPrimaryKey();
	}
	
	public function getUniqueRestrictions() {
		$primaries    = $this->table->getPrimaryKey();
		$restrictions = Array();
		
		foreach($primaries as $primary) {
			$r = $this->restrictionInstance($primary, $this->src[$primary->getName()]);
			$restrictions[] = $r;
		}
		
		return $restrictions;
	}
	
	public function getChildren($table) {
		$query = $this->queryInstance($table);
		$query->setParent($this);
		return $query;
	}
	
	public function getTable() {
		return $this->table;
	}

	public function __set($field, $value) {
		
		if ($value instanceof databaseRecord) {
			$primary = $value->getUniqueFields();
			foreach($primary as $pk) {
				$this->{"{$field}_{$pk->getName()}"} = $value->{$pk->getName()};
			}
			return;
		}
		
		if (!isset($this->data[$field]) || $value != $this->data[$field]) 
			$this->synced = false;
		
		if ($this->table->getField($field)) {
			$this->data[$field] = $value;
		}
		else throw new privateException ('Setting non-existent database field: ' . $field);

	}
	
	public function __get($field) {
		return $this->data[$field];
	}
	
	
	
	public abstract function delete();
	public abstract function insert();
	public abstract function update();
	public abstract function restrictionInstance(DBField$field, $value, $operator = null);
	public abstract function queryInstance($table);
	
	/**
	 * Increments a value on high read/write environments. Using update can
	 * cause data to be corrupted. Increment requires the data to be in sync
	 * aka. stored to database.
	 * 
	 * @param String $key
	 * @param int|float $diff
	 * @throws privateException
	 */
	public abstract function increment($key, $diff = 1);
}