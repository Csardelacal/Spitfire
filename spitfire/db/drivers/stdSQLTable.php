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
	
	/**
	 * Creates a list of definitions for CONSTRAINTS defined by the references
	 * this table's model makes to other models.
	 * 
	 * @return array
	 */
	private function foreignKeyDefinitions() {
		
		$ret = Array();
		$refs = $this->model->getReferencedModels();
		
		if (empty($refs)) return Array();
		
		foreach ($refs as $ref) {
			//Check the integrity of the remote table
			$this->getDb()->table($ref)->repair();
			#Get the fields the model references from $ref
			$referencedfields = $this->model->getReferencedFields($ref);
			#Get the table that represents $ref
			$referencedtable = $this->getDb()->table($ref);
			//Prepare the statement
			$refstt = sprintf('FOREIGN KEY (%s) REFERENCES %s(%s)',
				implode(', ', $referencedfields),
				$referencedtable->getTablename(),
				implode(', ', $ref->getPrimary()) 
				);
			
			$ret[] = $refstt;
		}
		
		return $ret;
	}

	public function create() {
		
		$definitions = $this->columnDefinitions();
		$foreignkeys = $this->foreignKeyDefinitions();
		$pk = array_keys($this->getPrimaryKey());
		
		if (!empty($pk)) $definitions[] = 'PRIMARY KEY(' . implode(', ', $pk) . ')';
		
		if (!empty($foreignkeys)) $definitions = array_merge ($definitions, $foreignkeys);
		
		$definitions = array_filter($definitions);
		
		$stt = sprintf('CREATE TABLE %s (%s)',
			$this->getTablename(),
			implode(', ', $definitions)
			);
		
		return $this->getDb()->execute($stt);
	}
}