<?php

namespace spitfire\storage\database\drivers;

use spitfire\storage\database\Table;
use spitfire\storage\database\Query;
use databaseRecord;

abstract class stdSQLDriver
{
	/**
	 * This generates a standard WHERE statement for SQL.
	 * 
	 * @param _SF_DBTable $table
	 * @param _SF_DBQuery $query
	 * @param mixed $fields
	 * @return String the prepared query statement
	 */
	public function query(Table $table, Query $query, $fields = null) {
		
		#Declare vars
		$rpp          = $query->getResultsPerPage();
		$offset       = ($query->getPage() - 1) * $rpp;
		$_join        = $query->getJoin();
		
		$selectstt    = 'SELECT';
		$fields       = ($fields)? $fields : $table->getFields();
		$fromstt      = 'FROM';
		$tablename    = "`{$table->getTablename()}`";
		$join         = '';
		$wherestt     = 'WHERE';
		$restrictions = $query->getRestrictions();
		$orderstt     = 'ORDER BY';
		$order        = $query->getOrder();
		$limitstt     = 'LIMIT';
		$limit        = $offset . ', ' . $rpp;
		
		#Inform the restrictions they are used by SQL
		foreach($restrictions as $r) $r->setStringify(Array($this, 'stringifyRestriction'));
		
		#Unset unneeded data & prepare for writing
		if (empty($fields)) {
			$fields = '*';
		}
		else {
			$fields = implode(', ', $fields);
		}
		
		if (empty($restrictions)) {
			$restrictions = '1';
		}
		elseif (count($restrictions) == 1) {
			$restrictions = (string) reset($restrictions);
		}
		else {
			$restrictions = implode(') OR (', $restrictions);
			$restrictions = "($restrictions)";
		}
		
		#Join specific tasks
		if ($_join) {
			$rem_table = $_join->getTable();
			$remote    = $_join->getUniqueRestrictions();
			$remotef   = $_join->getUniqueFields();
			
			foreach($remote as $r) $r->setStringify(Array($this, 'stringifyRestriction'));
			foreach($remotef as &$r) $r = $r->getName();
			//$remotef   = $this->escapeFieldNames($rem_table, $remotef);
			
			$join = 'RIGHT JOIN ' . $rem_table->getTableName();
			$join.= ' USING (' . implode(', ', $remotef) . ')';
			$restrictions = implode(' AND ', $remote) . " AND ($restrictions)";
		}
		
		if ($rpp < 0) {
			$limitstt = '';
			$limit    = '';
		}
		
		if (empty($order)) {
			$orderstt = '';
			$order    = '';
		}
		
		$stt = array_filter(Array( $selectstt, $fields, $fromstt, $tablename, $join, 
		    $wherestt, $restrictions, $orderstt, $order, $limitstt, $limit));
		
		return implode(' ', $stt);
		
	}
	
	/**
	 * 
	 * Creates a SQL statement for database entry deletion. It will delete
	 * 
	 * 
	 * @param _SF_DBTable $table
	 * @param type $primaries
	 * @return type
	 */
	public function delete(Table $table, databaseRecord $record) {
		
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
	 * @param databaseRecord $record
	 * @param string $field
	 * @return string
	 */
	public function inc(Table $table, databaseRecord $record, $field, $diff) {
		
		#Prepare vars
		$updatestt = 'UPDATE';
		$tablename = "`{$table->getTablename()}`";
		$setstt    = 'SET';
		$_field    = "$field";
		$equalsstt = "= $field + ?";
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
	 * @param databaseRecord $record
	 * @return string
	 */
	public function insert(Table $table, databaseRecord $record) {
		
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
	 * @param databaseRecord $record
	 * @return string
	 */
	public function update(Table$table, databaseRecord$record) {
		
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
	
}
