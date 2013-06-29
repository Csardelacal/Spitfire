<?php

namespace spitfire\storage\database\drivers;

use spitfire\storage\database\DB;
use spitfire\storage\database\Table;
use Model;

abstract class stdSQLDriver extends DB
{
	
	/**
	 * 
	 * Creates a SQL statement for database entry deletion. It will delete
	 * 
	 * 
	 * @param  Table $table
	 * @param  Model $record
	 * @return type
	 */
	public function delete(Table $table, Model $record) {
		
		#Prepare Vars
		$deletestt    = 'DELETE FROM';
		$tablename    = "`{$table->getTablename()}`";
		$wherestt     = 'WHERE';
		$where        = implode(' AND ', $record->getUniqueRestrictions());
		
		
		#Make it one string
		$stt = array_filter(Array($deletestt, $tablename, $wherestt,
					$where));
		
		return implode(' ', $stt);
	}
	
	/**
	 * Creates a 'standard' SQL for incrementing a field. Incrementing is a 
	 * slightly special operation as it cannot be replaced with a normal
	 * UPDATE statement. When being used by concurrent threads or processes
	 * it can happen that data is read twice before being written, therefore
	 * causing that data to become inconsistent.
	 * 
	 * @param _SF_DBTable $table
	 * @param Model $record
	 * @param string $field
	 * @return string
	 */
	public function inc(Table $table, Model $record, $field, $diff) {
		
		#Prepare vars
		$updatestt = 'UPDATE';
		$tablename = "`{$table->getTablename()}`";
		$setstt    = 'SET';
		$_field    = "$field";
		$equalsstt = "= $field + " . $this->quote($diff);
		$wherestt  = 'WHERE';
		$where     = implode(' AND ', $record->getUniqueRestrictions());
		
		
		#Make it one string
		$stt = array_filter(Array($updatestt, $tablename, $setstt, $_field,
					$equalsstt, $wherestt, $where));
		
		return implode(' ', $stt);
	}
	
	/**
	 * Creates a 'standard' insert statement for a SQL database. It will only
	 * use the data that is registered in the record.
	 * 
	 * @param _SF_DBTable $table
	 * @param Model $record
	 * @return string
	 */
	public function insert(Table $table, Model $record) {
		
		#Additional vars
		$escapecb   = Array($table, 'getField');
		$values     = $record->getData();
		$_fields    = array_keys($values);
		$_fields    = array_map($escapecb, $_fields);
		$values     = array_map(Array($this, 'quote'), $values);
		
		#Prepare vars
		$insertstt  = 'INSERT INTO';
		$tablename  = "`{$table->getTablename()}`";
		$fields     = implode(', ', $_fields);
		$fields     = '(' . $fields . ')';
		$valuesstt  = 'VALUES';
		$values     = '(' . implode(', ', $values) . ')';
		
		
		#Make it one string
		$stt = array_filter(Array($insertstt, $tablename, $fields, $valuesstt,
				    $values));
		
		return implode(' ', $stt);
	}
	
	/**
	 * Creates a UPDATE string like *field* = ? for a prepared statement. In 
	 * order to alter this you need to extend this class and override this
	 * function.
	 * 
	 * @param mixed $fields
	 * @return mixed
	 */
	private function makeUpdates($fields) {
		$data = Array();
		
		foreach ($fields as $field => $value) {
			$data[] = "`$field` = {$this->quote($value)}";
		}
		
		return $data;
	}
	
	/**
	 * Generates a prepared update statement in SQL for the database to
	 * execute.
	 * 
	 * @param _SF_DBTable $table
	 * @param Model $record
	 * @return string
	 */
	public function update(Table$table, Model$record) {
		
		#Prepare vars
		$updatestt  = 'UPDATE';
		$tablename  = "`{$table->getTablename()}`";
		$setstt     = 'SET';
		$updates    = implode(', ', $this->makeUpdates($record->getDiff()));
		$wherestt   = 'WHERE';
		$restricts  = implode(' AND ', $record->getUniqueRestrictions());
		
		
		#Make it one string
		$stt = array_filter(Array($updatestt, $tablename, $setstt, $updates,
					  $wherestt, $restricts));
		
		return implode(' ', $stt);
	}
	
	public function stringifyRestriction($restriction) {
		if ( is_a($restriction, 'spitfire\storage\database\RestrictionGroup') ) {
			#Just join the groups with and's
			return implode(' AND ', $restriction->getRestrictions());
		}
		elseif( is_a($restriction, 'spitfire\storage\database\Restriction') ) {
			$field    = $restriction->getField();
			$operator = $restriction->getOperator();
			$value    = $restriction->getValue();
			
			#Special treatment for arrays
			if (is_array($value)) {
				$value = array_map(Array($this, 'quote'), $value);
				$value = '('. implode(', ', $value) . ')';
			}
			else {
				$value    = $this->quote($value);
			}
			return "$field $operator $value";
		}
	}
	
	public abstract function quote($text);
	public abstract function execute($statement, $attemptrepair = true);
	
}
