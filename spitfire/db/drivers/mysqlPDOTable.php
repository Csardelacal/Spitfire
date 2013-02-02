<?php

namespace spitfire\storage\database\drivers;

use spitfire\storage\database\Table;
use spitfire\model\Field;
use Exception;

class MysqlPDOTable extends Table
{
	protected $fieldClass = 'spitfire\storage\database\drivers\mysqlPDOField';
	
	public function __toString() {
		return "`{$this->tablename}`";
	}

	public function getFieldInstance(Table $t, $fieldname, Field $data) {
		return new mysqlPDOField($t, $fieldname, $data);
	}

	public function check() {
		$stt = "DESCRIBE `{$this->tablename}`";
		$fields = $this->getFields();
		//Fetch the DB Fields and create on error.
		try {
			$query = $this->getDb()->execute($stt);
		}
		catch(Exception $e) {
			return $this->create();
		}
		//Loop through the exiting fields
		while (false != ($f = $query->fetch())) {
			try {
				$field = $this->getField($f['Field']);
				unset($fields[$field->getName()]);
			}
			catch(Exception $e) {/*Ignore*/}
			
		}
		
		foreach($fields as $field) $field->add();
	}

	public function create() {
		
		$fields = $this->getFields();
		foreach ($fields as $name => $f) {
			$fields[$name] = $name . ' ' . $f->columnDefinition();
		}
		
		$pk = array_keys($this->getPrimaryKey());
		
		if (!empty($pk)) $pk = ', PRIMARY KEY(' . implode(', ', $pk) . ')';
		else $pk = '';
		
		$stt = "CREATE TABLE " . $this->getTablename();
		$stt.= "(";
		$stt.= implode(', ', $fields);
		$stt.= $pk;
		$stt.= ")";
		
		return $this->getDb()->execute($stt);
	}

	public function newRecord() {
		throw new \RuntimeException('Not implemented');
	}
}