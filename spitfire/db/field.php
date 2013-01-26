<?php

namespace spitfire\storage\database;

/**
 * Represents a table's field in a database. Contains information about the 
 * 
 * @author César de la Cal <cesar@magic3w.com>
 */
class Field
{
	private $table;
	private $name;
	private $primary;
	private $auto_increment;
	
	private $stringify;
	
	public function __construct(_SF_DBTable$table, $name, $primary = false, $auto_increment = false) {
		
		$this->table          = $table;
		$this->name           = $name;
		$this->primary        = $primary;
		$this->auto_increment = $auto_increment;
		
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function getTable() {
		return $this->table;
	}
	
	public function isAutoIncrement() {
		return $this->auto_increment;
	}
	
	public function isPrimary() {
		return $this->primary;
	}
	
	public function setStringify($callback) {
		$this->stringify = $callback;
	}
	
	public function __toString() {
		if ($this->stringify) {
			$data = Array($this->table, $this->name);
			return call_user_func_array ($this->stringify, $data);
		}
		else {
			return "`{$this->table->getTablename()}`.`{$this->name}`";
		}
	}
}
