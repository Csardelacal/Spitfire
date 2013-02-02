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

	public function getFieldInstance(Table $t, Field $data) {
		return new mysqlPDOField($t, $data);
	}

	public function repair() {
		$stt = "DESCRIBE `{$this->tablename}`";
		$fields = $this->getFields();
		//Fetch the DB Fields and create on error.
		try {
			$query = $this->getDb()->execute($stt, false);
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
		echo 'Creating: ' . $this->getTablename();
		
		$fields = $this->getFields();
		$ref = '';
		foreach ($fields as $name => $f) {
			if (null != ($r = $f->getReference)) {
				$r->getTable()->repair();
			}
			$fields[$name] = $name . ' ' . $f->columnDefinition();
		}
		
		$refs = $this->model->getReferencedModels();
		$refstt = '';
		if (!empty($refs)) {
			foreach ($refs as $ref) {
				$this->getDb()->table($ref->getName())->repair();
				$refstt.= ', FOREIGN KEY(' . implode(', ', $this->model->getReferencedFields()) . ')
					REFERENCES '. $this->db->table($ref->getName())->getTableName() . '(' . implode(', ', $ref->getPrimary()) . ')';

			}
		}
		
		$pk = array_keys($this->getPrimaryKey());
		
		if (!empty($pk)) $pk = ', PRIMARY KEY(' . implode(', ', $pk) . ')';
		else $pk = '';
		
		$stt = "CREATE TABLE " . $this->getTablename();
		$stt.= "(";
		$stt.= implode(', ', $fields);
		$stt.= $pk;
		$stt.= $refstt;
		$stt.= ")";
		
		return $this->getDb()->execute($stt);
	}

	public function newRecord() {
		throw new \RuntimeException('Not implemented');
	}
}