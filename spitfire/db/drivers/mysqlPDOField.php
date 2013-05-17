<?php

namespace spitfire\storage\database\drivers;

use spitfire\model\Field;
use spitfire\storage\database\DBField;

class mysqlPDOField extends DBField
{
	
	public function columnType() {
		switch ($this->getDataType()) {
			case Field::TYPE_INTEGER:
				return 'INT(11)';
			case Field::TYPE_LONG:
				return 'BIGINT';
			case Field::TYPE_STRING:
				return "VARCHAR({$this->length})";
			case Field::TYPE_FILE:
				return "VARCHAR(255)";
			case Field::TYPE_TEXT:
				return "TEXT";
			case Field::TYPE_DATETIME:
				return "DATETIME";
		}
	}
	
	public function columnDefinition() {
		$definition = $this->columnType();
		$definition.= " NOT NULL ";
		
		if ($this->isAutoIncrement()) $definition.= "AUTO_INCREMENT ";
		if ($this->isUnique())        $definition.= "UNIQUE ";
		
		if (null != $ref = $this->getReferencedField()) {
			$definition.= 'REFERENCES ' . $ref . ' ON DELETE CASCADE ON UPDATE CASCADE';
		}
		
		return $definition;
	}

	public function add() {
		$stt = "ALTER TABLE `{$this->table->getTableName()}` 
			ADD COLUMN (`{$this->getName()}` {$this->columnDefinition()} )";
		$this->table->getDb()->execute($stt);
		
		if ($this->isPrimary()) {
			$pk = implode(', ', array_keys($this->table->getPrimaryKey()));
			$stt = "ALTER TABLE {$this->table->getTableName()} 
				DROP PRIMARY KEY, 
				ADD PRIMARY KEY(" . $pk . ")";
			$this->table->getDb()->execute($stt);
		}
	}

	public function __toString() {
		return "`{$this->table->getTableName()}`.`$this->name`";
	}
	
}