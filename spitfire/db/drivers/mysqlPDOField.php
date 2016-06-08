<?php

namespace spitfire\storage\database\drivers;

use spitfire\model\Field;
use spitfire\storage\database\DBField;
use \Reference;

class mysqlPDOField extends DBField
{
	
	public function columnType() {
		$logical = $this->getLogicalField();
		
		if ($logical instanceof Reference) { 
			$referenced = $this->getReferencedField();
			while($referenced->getReferencedField()) { $referenced = $referenced->getReferencedField(); }
			
			$logical = $referenced->getLogicalField(); 
		}
		
		switch ($logical->getDataType()) {
			case Field::TYPE_INTEGER:
				return 'INT(11)';
			case Field::TYPE_LONG:
				return 'BIGINT';
			case Field::TYPE_STRING:
				return "VARCHAR({$logical->getLength()})";
			case Field::TYPE_FILE:
				return "VARCHAR(255)";
			case Field::TYPE_TEXT:
				return "TEXT";
			case Field::TYPE_DATETIME:
				return "DATETIME";
			case Field::TYPE_BOOLEAN:
				return "TINYINT(4)";
		}
	}
	
	public function columnDefinition() {
		$definition = $this->columnType();
		
		if (!$this->getLogicalField()->getNullable())    $definition.= " NOT NULL ";
		if ($this->getLogicalField()->isAutoIncrement()) $definition.= "AUTO_INCREMENT ";
		if ($this->getLogicalField()->isUnique())        $definition.= "UNIQUE ";
		
		/*if (null != $ref = $this->getReferencedField()) {
			$definition.= 'REFERENCES ' . $ref . ' ON DELETE CASCADE ON UPDATE CASCADE';
		}/**/
		
		return $definition;
	}

	public function add() {
		$stt = "ALTER TABLE `{$this->getTable()->getTableName()}` 
			ADD COLUMN (`{$this->getName()}` {$this->columnDefinition()} )";
		$this->getTable()->getDb()->execute($stt);
		
		if ($this->getLogicalField()->isPrimary()) {
			$pk = implode(', ', array_keys($this->getTable()->getPrimaryKey()));
			$stt = "ALTER TABLE {$this->getTable()->getTableName()} 
				DROP PRIMARY KEY, 
				ADD PRIMARY KEY(" . $pk . ")";
			$this->getTable()->getDb()->execute($stt);
		}
	}

	public function __toString() {
		return "`{$this->getTable()->getTableName()}`.`{$this->getName()}`";
	}
	
}