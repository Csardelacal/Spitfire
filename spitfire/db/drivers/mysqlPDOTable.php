<?php

namespace spitfire\storage\database\drivers;

use spitfire\storage\database\Table;
use spitfire\storage\database\Query;
use spitfire\storage\database\DBField;
use spitfire\model\Field;
use Model;
use Exception;

/**
 * Represents table specific properties and methods for the MySQLPDO Driver.
 */
class MysqlPDOTable extends stdSQLTable
{
	
	/**
	 * Returns the name of a table as DB Object reference (with quotes).
	 * 
	 * @return string The name of the table escaped and ready for use inside
	 *                of a query.
	 */
	public function __toString() {
		return "`{$this->tablename}`";
	}

	/**
	 * Creates a new MySQL PDO Field object.
	 * 
	 * @param \spitfire\storage\database\Table $t
	 * @param \spitfire\model\Field $data
	 * @return \spitfire\storage\database\drivers\mysqlPDOField
	 */
	public function getFieldInstance(Field$field, $name, DBField$references = null) {
		return new mysqlPDOField($field, $name, $references);
	}

	public function repair() {
		$stt = "DESCRIBE $this";
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

	public function getQueryInstance() {
		return new MysqlPDOQuery($this);
	}
	
	/**
	 * Deletes this record from the database. This method's call cannot be
	 * undone. <b>[NOTICE]</b>Usually Spitfire will cascade all the data
	 * related to this record. So be careful calling it deliberately.
	 * 
	 * @param Model $record Database record to be deleted from the DB.
	 * 
	 */
	public function delete(Model$record) {
		$table = $this;
		$db    = $table->getDb();
		$key   = $record->getPrimaryData();
		
		$restrictions = Array();
		foreach ($key as $k => $v) {$restrictions[] = "$k = $v";}
		
		$stt = sprintf('DELETE FROM %s WHERE %s',
			$table,
			implode(' AND ', $restrictions)
			);
		$db->execute($stt);
	}

	/**
	 * Modifies this record on high write environments. If two processes modify
	 * this record simultaneously they won't generate unconsistent data.
	 * This function is especially useful for counters i.e. pageviews, clicks,
	 * plays or any kind of transactions.
	 * 
	 * @throws privateException If the database couldn't handle the request.
	 * @param Model $record Database record to be modified.
	 * @param string $key
	 * @param int|float|double $diff
	 */
	public function increment(Model$record, $key, $diff = 1) {
		
		$table = $this;
		$db    = $table->getDb();
		$key   = $record->getPrimaryData();
		
		$restrictions = Array();
		foreach ($key as $k => $v) {$restrictions[] = "$k = $v";}
		
		$stt = sprintf('UPDATE %s SET `%s` = `%s` + %s WHERE %s',
			$table, 
			$key,
			$key,
			$db->quote($diff),
			implode(' AND ', $restrictions)
		);
		
		$db->execute($stt);
	}

	public function insert(Model$record) {
		$data = $record->getData();
		$table = $record->getTable();
		$db = $table->getDb();
		
		$write = Array();
                
		foreach ($data as $value) {
			$write = array_merge($write, $value->dbGetData());
		}
		
		$fields = array_keys($write);
		foreach ($fields as &$field) $field = '`' . $field . '`';
		unset($field);
		
		$quoted = array_map(Array($db, 'quote'), $write);
		
		$stt = sprintf('INSERT INTO %s (%s) VALUES (%s)',
			$table,
			implode(', ', $fields),
			implode(', ', $quoted)
			);
		$db->execute($stt);
		return $db->getConnection()->lastInsertId();
	}

	public function update(Model$record) {
		$data  = $record->getData();
		$table = $record->getTable();
		$db    = $table->getDb();
		$key   = $record->getPrimaryData();
		
		$restrictions = Array();
		foreach ($key as $k => $v) {$restrictions[] = "$k = {$db->quote($v)}";}
		
		$write = Array();
                
		foreach ($data as $value) {
			$write = array_merge($write, $value->dbGetData());
		}
		
		$quoted = Array();
		foreach ($write as $f => $v) $quoted[] = "{$table->getField($f)} = {$db->quote($v)}";
		
		$stt = sprintf('UPDATE %s SET %s WHERE %s',
			$table, 
			implode(', ', $quoted),
			implode(' AND ', $restrictions)
		);
		
		$this->getDb()->execute($stt);
		
	}

	public function restrictionInstance($query, DBField$field, $value, $operator = null) {
		return new MysqlPDORestriction($query,	$field, $value, $operator);
	}

	public function queryInstance($table) {
		if (!$table instanceof Table) throw new \privateException('Need a table object');
		
		return new MysqlPDOQuery($table);
	}

	public function destroy() {
		$this->getDb()->execute('DROP TABLE ' . $this);
	}
}