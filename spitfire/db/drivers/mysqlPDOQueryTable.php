<?php

namespace spitfire\storage\database\drivers;

use spitfire\storage\database\QueryTable;

class MysqlPDOQueryTable extends QueryTable
{
	/**
	 * 
	 * @todo Move the aliasing thing over to the queryTable completely.
	 * @return string
	 */
	public function __toString() {
		return $this->getQuery()->getAliased()? 
				  "`{$this->getTable()->getTablename()}_{$this->getQuery()->getId()}`" : 
				  "`{$this->getTable()->getTablename()}`";
	}

	public function definition() {
		return "{$this->getTable()} AS {$this}";
	}
}
