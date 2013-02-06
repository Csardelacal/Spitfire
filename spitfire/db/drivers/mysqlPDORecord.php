<?php

namespace spitfire\storage\database\drivers;

use \databaseRecord;
use \spitfire\storage\database\DBField;

class MysqlPDORecord extends databaseRecord
{
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

	public function increment($key, $diff = 1) {
		throw new \BadMethodCallException('Unimplemented Method: increment');
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
}
