<?php

namespace spitfire\storage\database\drivers;

use spitfire\storage\database\Table;
use spitfire\model\Field;

class MysqlPDOTable extends Table
{
	protected $fieldClass = 'spitfire\storage\database\drivers\mysqlPDOField';
	
	public function __toString() {
		return "`{$this->tablename}`";
	}

	public function getFieldInstance(Table $t, $fieldname, Field $data) {
		return new mysqlPDOField($t, $fieldname, $data);
	}
}