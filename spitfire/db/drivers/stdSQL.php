<?php

abstract class _SF_stdSQLDriver
{
	/**
	 * This generates a standard WHERE statement for SQL. Remember that this
	 * assumes prepared statements to be available for your driver and
	 * therefore will return the fields replaced by a question mark (?) for
	 * the driver to be replaced.
	 * 
	 * To avoid this behaviour you need to change the way restrictions
	 * generate their output.
	 * 
	 * 
	 * @param _SF_DBTable $table
	 * @param _SF_DBQuery $query
	 * @param mixed $fields
	 * @return String the prepared query statement
	 */
	public function query(_SF_DBTable $table, _SF_DBQuery $query, $fields = null) {
		
		#Declare vars
		$rpp          = $query->getResultsPerPage();
		$offset       = ($query->getPage() - 1) * $rpp;
		
		$selectstt    = 'SELECT';
		$fields       = ($fields)? $fields : $table->getFields();
		$fromstt      = 'FROM';
		$tablename    = "`{$table->getTablename()}`";
		$wherestt     = 'WHERE';
		$restrictions = $query->getRestrictions();
		$orderstt     = 'ORDER BY';
		$order        = $query->getOrder();
		$limitstt     = 'LIMIT';
		$limit        = $offset . ', ' . $rpp;
		
		#Unset unneeded data & prepare for writing
		if (empty($fields)) {
			$fields = '*';
		}
		else {
			array_walk ($fields, Array($this, 'escapeFieldName'));
			$fields = implode(', ', $fields);
		}
		
		if (empty($restrictions)) {
			$restrictions = '1';
		}
		elseif (count($restrictions) == 1) {
			$restrictions = (string)$restrictions;
		}
		else {
			$restrictions = implode(') OR (', $restrictions);
			$restrictions = "($restrictions)";
		}
		
		if ($rpp < 0) {
			$limitstt = '';
			$limit    = '';
		}
		
		if (empty($order)) {
			$orderstt = '';
			$order    = '';
		}
		
		$stt = array_filter(Array( $selectstt, $fields, $fromstt, $tablename, 
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
	public function delete(_SF_DBTable $table, databaseRecord $record) {
		
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
	public function inc(_SF_DBTable $table, databaseRecord $record, $field) {
		
		#Prepare vars
		$updatestt = 'UPDATE';
		$tablename = "`{$table->getTablename()}`";
		$setstt    = 'SET';
		$_field    = "`$field`";
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
	public function insert(_SF_DBTable $table, databaseRecord $record) {
		
		#Additional vars
		$escapecb   = Array($table->getDb(), 'escapeFieldName');
		$_fields    = array_keys($record->getData());
		array_walk($_fields, $escapecb);
		
		#Prepare vars
		$insertstt  = 'INSERT INTO';
		$tablename  = "`{$table->getTablename()}`";
		$fields     = implode(', ', $_fields);
		$fields     = '(' . $fields . ')';
		$valuesstt  = 'VALUES';
		$values     = '(' . implode(', ', array_fill(0, count($_fields), '?')) . ')';
		
		
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
			$data[] = "`$field` = ?";
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
	public function update(_SF_DBTable$table, databaseRecord$record) {
		
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
	
}
