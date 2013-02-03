<?php

namespace spitfire\storage\database\drivers;

use spitfire\storage\database\Table;

abstract class stdSQLTable extends Table
{
	
	/**
	 * Creates the column definitions for each column
	 * 
	 * @return mixed
	 */
	private function columnDefinitions() {
		$fields = $this->getFields();
		foreach ($fields as $name => $f) {
			$fields[$name] = $name . ' ' . $f->columnDefinition();
		}
		return $fields;
	}
	
	private function foreignKeyDefinitions() {
		
		$ret = Array();
		$refs = $this->model->getReferencedModels();
		
		if (empty($refs)) return Array();
		
		foreach ($refs as $ref) {
			//Check the integrity of the remote table
			$this->getDb()->table($ref)->repair();
			//Prepare the statement
			$refstt.= 'FOREIGN KEY(' . 
				implode(', ', $this->model->getReferencedFields()) . 
				') REFERENCES '. 
				$this->db->table($ref)->getTableName() . 
				'(' . implode(', ', $ref->getPrimary()) . ')';
			$ret[] = $refstt;
		}
		
		return $ret;
	}

	public function create() {
		
		$definitions = $this->columnDefinitions();
		$foreignkeys = $this->foreignKeyDefinitions();
		
		
		$pk = array_keys($this->getPrimaryKey());
		
		if (!empty($pk)) $pk = ', PRIMARY KEY(' . implode(', ', $pk) . ')';
		else $pk = '';
		
		if (!empty($foreignkeys)) $foreignkeys = ', ' . implode (', ', $foreignkeys);
		else $foreignkeys = '';
		
		$stt = "CREATE TABLE " . $this->getTablename();
		$stt.= "(";
		$stt.= implode(', ', $definitions);
		$stt.= $pk;
		$stt.= $foreignkeys;
		$stt.= ")";
		
		return $this->getDb()->execute($stt);
	}
}