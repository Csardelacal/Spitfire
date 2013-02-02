<?php

namespace spitfire\storage\database;

use spitfire\model\Field;
use spitfire\storage\database\Table;

abstract class DBField extends Field
{
	
	protected $table;
	protected $name;
	
	public function __construct(Table$table, Field$field) {
		$this->table = $table;
		$this->importRawData($field);
	}
	
	public function getTable() {
		return $this->table;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function getReference() {
		list($model, $field) = parent::getReference();
		if (!$model) return false;
		$db = $this->getTable()->getDb();
		return $db->table($model->getName())->getField($field);
	}


	abstract public function columnDefinition();
	abstract public function columnType();
	abstract public function add();
	
}