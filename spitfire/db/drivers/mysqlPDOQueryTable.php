<?php

namespace spitfire\storage\database\drivers;

use spitfire\storage\database\QueryTable;

class MysqlPDOQueryTable extends QueryTable
{
	public function __toString() {
		return "`{$this->getTable()->getTablename()}_{$this->getQuery()->getId()}`";
	}

	public function definition() {
		return "{$this->getTable()} AS {$this}";
	}
}