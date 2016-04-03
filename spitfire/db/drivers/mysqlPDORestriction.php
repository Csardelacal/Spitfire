<?php

namespace spitfire\storage\database\drivers;

use \spitfire\storage\database\Restriction;
use \spitfire\storage\database\Query;
use \Exception;
use \Model;
use \privateException;

class MysqlPDORestriction extends Restriction
{
	public function __toString() {
		
		try {
			
			if (!$this->getField() instanceof \spitfire\storage\database\QueryField)
				throw new \privateException();
			
			$value = $this->getValue();
			$field = $this->getField()->getField();
			
			if ($field instanceof \Reference || $field instanceof \ManyToManyField || $field instanceof \ChildrenField) {
				
				if ($value instanceof Model) {
					$value = $value->getQuery();
				}
				
				if ($value instanceof Query) {
					return sprintf('(%s)', implode(' AND ', $value->getRestrictions()));
				}
				
				elseif ($value === null) {
					return "`{$this->getQuery()->getTable()}`.`{$this->getField()->getName()}` {$this->getOperator()} null}";
				}
				
				else {
					throw new privateException("Invalid data");
				}
				
			}
			
			else {
			
				if (is_array($value)) {
					foreach ($value as &$v) {
						$v = $this->getTable()->getDb()->quote($v);
					}

					$quoted = implode(',', $value);
					return "{$this->getField()} {$this->getOperator()} ({$quoted})";
				}
				
				elseif ($value instanceof MysqlPDOQueryField) {
					return "{$this->getField()} {$this->getOperator()} {$this->getValue()}";
				}

				else {
					$quoted = $this->getTable()->getDb()->quote($this->getValue());
					return "{$this->getField()} {$this->getOperator()} {$quoted}";
				}
			}
			
		}catch (Exception $e) {
			error_log($e->getMessage());
			error_log($e->getTraceAsString());
			return '';
		}
	}
	
}
