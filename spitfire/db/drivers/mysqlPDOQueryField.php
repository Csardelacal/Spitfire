<?php

namespace spitfire\storage\database\drivers;

use spitfire\storage\database\QueryField;

class MysqlPDOQueryField extends QueryField
{
	public function __toString() {
		return "{$this->getQuery()->getTable()}.`{$this->getField()->getName()}`";
	}
}