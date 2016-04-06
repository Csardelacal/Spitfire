<?php namespace spitfire\storage\database\drivers;

use spitfire\storage\database\QueryTable;

class MysqlPDOQueryTable extends QueryTable
{
	/**
	 * 
	 * @todo Move the aliasing thing over to the queryTable completely.
	 * @return string
	 */
	public function __toString() {
		return "`{$this->getAlias()}`";
	}

	public function definition() {
		return "{$this->getTable()} AS `{$this->getAlias()}`";
	}
}
