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
	
	public function getReferencedField() {
		$ref   = parent::getReference();
		$field = parent::getReferencedField();
		$db = $this->getTable()->getDb();
		
		if (!$ref) return null;
		
		return $db->table($ref->getTarget())->getField($field->getName());
	}


	abstract public function columnDefinition();
	abstract public function columnType();
	abstract public function add();
	
}