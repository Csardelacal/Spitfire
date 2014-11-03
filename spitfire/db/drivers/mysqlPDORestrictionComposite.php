<?php

namespace spitfire\storage\database\drivers;

use \spitfire\storage\database\CompositeRestriction;

class MysqlPDOCompositeRestriction extends CompositeRestriction
{
	
	public function __toString() {
		//TODO: This should just print the PK IS / IS NOT NULL
		if ($this->getField() === null || $this->getValue() === null) {
			return implode(' AND ', $this->getSimpleRestrictions());
		} else {
			$fields = $this->getValue()->getQueryTable()->getTable()->getPrimaryKey();
			$_ret   = Array();
			
			foreach($fields as $field) {
				$f = $this->getValue()->queryFieldInstance($field);
				$o = $this->getOperator() === '='? 'IS NOT' : 'IS';
				$_ret[] = "{$f} {$o} NULL";
			}
			
			return implode(' AND ', $_ret);
		}
	}
	
}