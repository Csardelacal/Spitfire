<?php

namespace spitfire\storage\database\drivers;

use \spitfire\storage\database\Restriction;
use \spitfire\storage\database\Query;
use \Exception;

class MysqlPDORestriction extends Restriction
{
	public function __toString() {
		try {
			if(is_a($this->getValue(), 'Model')) {
				$fields = $this->getField()->getPhysical();
				$data = $this->getValue()->getPrimaryData();
				
				$restrictions = Array();
				
				while (null != $field = array_shift($fields)) {
					$restrictions[] = new MysqlPDORestriction($field, array_shift($data), $this->getOperator());
				}
				return implode(' AND ', $restrictions);
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