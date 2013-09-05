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
			$value = $this->getValue();
			$field = $this->getField()->getField();
			
			if ($field instanceof \Reference || $field instanceof \ManyToManyField || $field instanceof \ChildrenField) {
				
				if ($value instanceof Model) {
					return implode(' AND ', $value->getQuery()->getRestrictions());
				}
				
				elseif ($value instanceof Query) {
					return implode(' AND ', $value->getRestrictions());
				}
				
				elseif ($value === null) {
					return "`{$this->getTableName()}`.`{$this->getField()->getName()}` {$this->getOperator()} null}";
				}
				
				else {
					throw new privateException("Invalid data");
				}
				
			}
			
			else {
			
				if (is_array($value)) {
					foreach ($value as &$v)
						$v = $this->getTable()->getDb()->quote($value);

					$quoted = implode(',', $value);
					return "`{$this->getField()}` {$this->getOperator()} ({$quoted})";
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
	
	public function queryJoin($value, $query, $field, $type = null) {
		if (!$query instanceof Query) throw new \privateException("oops");
		if (!$value instanceof Query) throw new \privateException("oops");
		return new MysqlPDOJoin($value, $query, $field, $type);
	}
	
	public function getTableName() {
		if ($this->getQuery()) return $this->getQuery()->getAlias();
		else return $this->getTable()->getTableName();
	}
}