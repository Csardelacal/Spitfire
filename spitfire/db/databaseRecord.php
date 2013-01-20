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
	
	#Status vars
	private $synced;
	private $deleted;
	
	public function __construct(_SF_DBTable $table, $srcData = Array() ) {
		$this->src     = $srcData;
		$this->data    = $srcData;
		$this->table   = $table;
		
		$this->synced  = true;
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
	
	public function delete() {
		$this->table->delete($this);
		$this->deleted = true;
	}
	
	public function isSynced() {
		return $this->synced && !$this->deleted;
	}
	
	/**
	 * Increments a value on high read/write environments. Using update can
	 * cause data to be corrupted. Increment requires the data to be in sync
	 * aka. stored to database.
	 * 
	 * @param String $key
	 * @param int|float $diff
	 * @throws privateException
	 */
	public function increment($key, $diff = 1) {
		if (!$this->isSynced()) throw new privateException('Data is out of sync. Needs to be stored before increment');
		
		$this->table->getDb()->inc($this->table, $this, $key, $diff);
		#Should the database update fail it'll throw an exception and break
		$this->data[$key] = $this->data[$key] + $diff;
		
	}
	
	public function store() {
		
		if (empty($this->src)) {
			$id = $this->table->insert($this);
			$ai = $this->table->getAutoIncrement();
			
			if ($ai && empty($this->data[$ai]) ) {
				$this->data[$ai] = $id;
			}
		}
		else {
			$this->table->update($this);
		}
		
		$this->synced = true;
	}
	
	public function getUniqueRestrictions() {
		$primaries    = $this->table->getPrimaryKey();
		$restrictions = Array();
		
		foreach($primaries as $primary) {
			$restrictions[] = new _SF_Restriction($primary, $this->data[$primary]);
		}
		
		return $restrictions;
	}

	public function __set($field, $value) {
		
		if ($value != $this->data[$field]) $this->synced = false;
		
		if (in_array($field, $this->table->getFields())) {
			$this->data[$field] = $value;
		}
		else throw new privateException ('Setting non-existent database field: ' . $field);

	}
	
	public function __get($field) {
		return $this->data[$field];
	}
	
}