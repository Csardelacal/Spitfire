<?php

namespace spitfire\storage\database\drivers;

use spitfire\model\Field;
use spitfire\storage\database\DBField;

class mysqlPDOField extends DBField
{
	
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

	public function __toString() {
		return "`{$this->table->getTableName()}`.`$this->name`";
	}
	
}