<?php

namespace spitfire\storage\database\drivers;

use spitfire\storage\database\Table;
use spitfire\model\Field;
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

	public function newRecord() {
		throw new \RuntimeException('Not implemented');
	}
}