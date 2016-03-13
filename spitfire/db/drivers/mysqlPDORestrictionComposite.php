<?php

namespace spitfire\storage\database\drivers;

use \spitfire\storage\database\CompositeRestriction;

class MysqlPDOCompositeRestriction extends CompositeRestriction
{
	
	public function __toString() {
		//TODO: This should just print the PK IS / IS NOT NULL
		$field = $this->getField();
		$value = $this->getValue();
		
		if ($field === null || $value === null) {
			return implode(' AND ', $this->getSimpleRestrictions());
		} elseif ($field instanceof \Reference && $this->getQuery()->getAliased()) {
			/*
			 * This section here is a very complicated one. When a query nests several
			 * layers of queries inside it, the composite restrictions stop using
			 * the "IS NULL" to String methods.
			 * 
			 * Instead of this quirk, it should be filtered out
			 */
			return '1';
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