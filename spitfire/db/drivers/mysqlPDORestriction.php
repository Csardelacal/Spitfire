<?php

namespace spitfire\storage\database\drivers;

use \spitfire\storage\database\Restriction;
use \Exception;

class MysqlPDORestriction extends Restriction
{
	public function __toString() {
		try {
			$quoted = $this->getTable()->getDb()->quote($this->getValue());
			return "{$this->getField()} {$this->getOperator()} {$quoted}";
		}catch (Exception $e) {
			error_log($e->getMessage());
			error_log($e->getTraceAsString());
			return '';
		}
	}
}