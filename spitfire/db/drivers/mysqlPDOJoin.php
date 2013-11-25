<?php

namespace spitfire\storage\database\drivers;

use \Exception;
use \spitfire\storage\database\Table;
use \spitfire\storage\database\DBField;
use \spitfire\storage\database\CompositeRestriction;

class MysqlPDOJoin
{
	private $restriction;
	
	public function __construct(CompositeRestriction$restriction) {
		$this->restriction = $restriction;
	}
	
	public function __toString() {
		if ($this->restriction->getValue() === null) return '';
		
		try {
			$restrictions = $this->restriction->getConnectingRestrictions();
			$_return      = '';
			
			if ($restrictions === null) {return '';}

			foreach ($restrictions as $simple_restrictions) {
				
				if ($simple_restrictions instanceof \spitfire\storage\database\RestrictionGroup) {
					$table = $simple_restrictions->getQuery()->getQueryTable();
					$_return.= sprintf("LEFT JOIN %s ON %s ", $table->definition(), $simple_restrictions);
				}
				else {
					throw new \privateException('Joins require restriction groups to be created.');
				}
			}

			return $_return;
		}
		catch(Exception $e) {
			echo $e->getMessage() . "\n";
			echo $e->getTraceAsString();
			die();
		}
		
	}
	
	public function aliasedTableDefinition(Table$table, $alias) {
		return sprintf('%s AS `%s`', $table , $alias);
	}
	
	public function aliasedField($tableAlias, DBField$field) {
		return sprintf('`%s`.`%s`', $tableAlias, $field->getName());
	}
	
}