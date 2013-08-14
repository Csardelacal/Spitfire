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
			$field = $this->getField();
			
			if ($field instanceof \Reference) {
				
				if ($value instanceof Model) {
					$primarykey    = $value->getPrimaryData();
					$fields        = $this->getField()->getPhysical();
					$_restrictions = Array();

					while (!empty($fields)) {
						$_restrictions[] = new MysqlPDORestriction($this->getQuery(), array_shift($fields), array_shift($primarykey));
					}

					return implode(' AND ', $_restrictions);
				}
				
				elseif ($value instanceof Query) {
					return implode(' AND ', $this->getValue()->getRestrictions());
				}
				
				elseif ($value === null) {
					return "`{$this->getTableName()}`.`{$this->getField()->getName()}` {$this->getOperator()} null}";
				}
				
				else {
					throw new privateException("References do not accept values other than Model");
				}
				
			}
			
			elseif ($field instanceof \ManyToManyField) {
				if ($value instanceof Model) {
					$primarykey    = $value->getPrimaryData();
					$fields        = $this->getField()->getPhysical();
					$_restrictions = Array();

					while (!empty($fields)) {
						$_restrictions[] = new MysqlPDORestriction($this->getQuery(), array_shift($fields), array_shift($primarykey));
					}

					return implode(' AND ', $_restrictions);
				}
				
				elseif ($value instanceof Query) {
					return implode(' AND ', $this->getValue()->getRestrictions());
				}
			}
			
			elseif ($field instanceof \ChildrenField) {
				
				if ($value instanceof Query) {
					return implode(' AND ', $this->getValue()->getRestrictions());
				}
				
			}
			
			else {
			
				if (is_array($value)) {
					foreach ($value as &$v)
						$v = $this->getTable()->getDb()->quote($value);

					$quoted = implode(',', $value);
					return "`{$this->getTableName()}`.`{$this->getField()->getName()}` {$this->getOperator()} ({$quoted})";
				}

				else {
					$quoted = $this->getTable()->getDb()->quote($this->getValue());
					return "`{$this->getTableName()}`.`{$this->getField()->getName()}` {$this->getOperator()} {$quoted}";
				}
			}
			
		}catch (Exception $e) {
			error_log($e->getMessage());
			error_log($e->getTraceAsString());
			return '';
		}
	}
	
	public function queryJoin($value, $query, $field) {
		if (!$query instanceof Query) throw new \privateException("oops");
		if (!$value instanceof Query) throw new \privateException("oops");
		return new MysqlPDOJoin($value, $query, $field);
	}
	
	public function getTableName() {
		if ($this->getQuery()) return $this->getQuery()->getAlias();
		else return $this->getTable()->getName();
	}
}