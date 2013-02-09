<?php

namespace spitfire\storage\database\drivers;

use \databaseRecord;
use \spitfire\storage\database\DBField;

class MysqlPDORecord extends databaseRecord
{
	/**
	 * Deletes this record from the database. This method's call cannot be
	 * undone. <b>[NOTICE]</b>Usually Spitfire will cascade all the data
	 * related to this record. So be careful calling it deliberately.
	 * 
	 */
	public function delete() {
		$table = $this->getTable();
		$db    = $table->getDb();
		$restrictions = $this->getUniqueRestrictions();
		
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
	 * @param string $key
	 * @param int|float|double $diff
	 */
	public function increment($key, $diff = 1) {
		
		$table = $this->getTable();
		$db = $table->getDb();
		
		$stt = sprintf('UPDATE %s SET `%s` = `%s` + %s WHERE %s',
			$table, 
			$key,
			$key,
			$db->quote($diff),
			implode(' AND ', $this->getUniqueRestrictions())
		);
		
		$db->execute($stt);
	}

	public function insert() {
		$data = $this->getData();
		$fields = array_keys($data);
		$table = $this->getTable();
		$db = $table->getDb();
		
		$quoted = array_map(Array($db, 'quote'), $data);
		
		$stt = sprintf('INSERT INTO %s (%s) VALUES (%s)',
			$table,
			implode(', ', $fields),
			implode(', ', $quoted)
			);
		$db->execute($stt);
		return $db->getConnection()->lastInsertId();
	}

	public function update() {
		$data = $this->getData();
		$table = $this->getTable();
		$db = $table->getDb();
		
		$quoted = Array();
		foreach ($data as $f => $v) $quoted[] = "$f = {$db->quote($v)}";
		
		$stt = sprintf('UPDATE %s SET %s WHERE %s',
			$table, 
			implode(', ', $quoted),
			implode(' AND ', $this->getUniqueRestrictions())
		);
		
	}

	public function restrictionInstance(DBField$field, $value, $operator = null) {
		return new MysqlPDORestriction($field, $value, $operator);
	}

	public function queryInstance($table) {
		return new MysqlPDOQuery($table);
	}
}
