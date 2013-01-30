<?php

namespace spitfire\storage\database\drivers;

use spitfire\storage\database\Table;

class MysqlPDOTable extends Table
{
	protected $fieldClass = 'spitfire\storage\database\drivers\mysqlPDOField';
	
	public function __toString() {
		return "`{$this->tablename}`";
	}
}