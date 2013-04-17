<?php

namespace spitfire\storage\database\drivers;

use \spitfire\storage\database\Restriction;
use \Exception;

class MysqlPDORestriction extends Restriction
{
	public function __toString() {
		try {
			if(is_a($this->getValue(), 'databaseRecord')) {
				$data = $this->getValue()->getUniqueRestrictions();
				$str  = '';
				foreach($data as $r) $str.= $r;
				return $str;
			}
			elseif (is_array($this->getValue())) {
				$values = $this->getValue();
				foreach ($values as &$value) {
					$value = $this->getTable()->getDb()->quote($value);
				}
				$quoted = implode(',', $values);
				return "{$this->getField()} {$this->getOperator()} ({$quoted})";
			}
			else {
				$quoted = $this->getTable()->getDb()->quote($this->getValue());
				return "{$this->getField()} {$this->getOperator()} {$quoted}";
			}
		}catch (Exception $e) {
			error_log($e->getMessage());
			error_log($e->getTraceAsString());
			return '';
		}
	}
}