<?php

namespace spitfire\storage\database\drivers;

use \spitfire\storage\database\Restriction;

class MysqlPDORestriction extends Restriction
{
	public function __toString() {
		return "{$this->getField()} {$this->getOperator()} {$this->getTable()->getDb()->quote($this->getValue())}";
	}
}