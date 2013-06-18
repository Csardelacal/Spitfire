<?php

namespace spitfire\storage\database\drivers;

use \spitfire\storage\database\Restriction;
use \spitfire\storage\database\Query;
use \Exception;

class MysqlPDORestriction extends Restriction
{
	public function __toString() {
		try {
			if(is_a($this->getValue(), 'databaseRecord')) {
				$fields = $this->getField()->getPhysical();
				$data = $this->getValue()->getPrimaryData();
				
				$str = '';
				
				while (null != $field = array_shift($fields)) {
					$str.= new MysqlPDORestriction($field, array_shift($data), $this->getOperator());
				}
				return $str;
			}
			elseif($this->getValue() instanceof Query) {
				return implode(' AND ', $this->getValue()->getRestrictions());
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