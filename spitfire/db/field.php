<?php

namespace spitfire\storage\database;

use spitfire\model\Field;
use spitfire\storage\database\Table;

abstract class DBField
{
	
	protected $table;
	protected $name;
	protected $field;
	
	public function __construct(Table$table, $name, Field$field) {
		$this->table = $table;
		$this->name  = $name;
		$this->field = $field;
	}
	
	public function getTable() {
		return $this->table;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function __call($fn, $params) {
		return call_user_func_array(Array($this->field, $fn), $params);
	}
	
	abstract public function columnDefinition();
	abstract public function columnType();
	abstract public function __toString();
	
}