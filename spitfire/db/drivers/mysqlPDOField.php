<?php

namespace spitfire\storage\database\drivers;

use spitfire\storage\database\Field;

class mysqlPDOField
{
	
	private $field;
	
	public function __construct(Field$field) {
		$this->field = $field;
	}
	
	public function columnType() {
		switch ($this->field->getDataType()) {
			case Field::TYPE_INTEGER:
				return 'INT';
			case Field::TYPE_LONG:
				return 'BIGINT';
			case Field::TYPE_STRING:
				return "VARCHAR({$this->field->getLength()})";
			case Field::TYPE_TEXT:
				return "TEXT";
		}
	}
	
	public function columnDefinition() {
		$definition = $this->columnType();
		$definition.= " NOT NULL ";
		
		if ($this->field->isAutoIncrement()) $definition.= "AUTO_INCREMENT ";
		if ($this->field->isUnique())        $definition.= "UNIQUE ";
		if ($this->field->isPrimary())       $definition.= "PRIMARY KEY ";
		
		if ($ref = $this->field->getReference()) {
			$definition.= 'REFERENCES ';
			$definition.= "`{$ref[0]->getName()}`";
			$definition.= '.';
			$definition.= "`$ref[1]`";
			$definition.= ' ON DELETE CASCADE ON UPDATE CASCADE';
		}
		
		return $definition;
	}
	
}