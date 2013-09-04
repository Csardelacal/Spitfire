<?php

namespace spitfire\storage\database\drivers;

use spitfire\storage\database\QueryTable;

class MysqlPDOQueryTable extends QueryTable
{
	public function __toString() {
		return "`{$this->getQuery()->getAlias()}`";
	}

	public function definition() {
		return "{$this->getTable()} AS {$this}";
	}
}