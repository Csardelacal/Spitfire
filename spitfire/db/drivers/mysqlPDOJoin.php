<?php

namespace spitfire\storage\database\drivers;

use \spitfire\storage\database\QueryJoin;
use \spitfire\storage\database\Table;
use \spitfire\storage\database\DBField;

class MysqlPDOJoin extends QueryJoin
{
	
	public function aliasedTableDefinition(Table$table, $alias) {
		return sprintf('%s AS `%s`', $table , $alias);
	}
	
	public function aliasedField($tableAlias, DBField$field) {
		return sprintf('`%s`.`%s`', $tableAlias, $field->getName());
	}
	
	public function __toString() {
		$bridge = $this->getBridge();
		
		if ($bridge) {
			$bridge      = $this->getBridge();
			$_physicstts = Array();
			$bridgealias = 'table_'. rand();

			$src_field = $bridge->getModel()->getField($this->getTargetQuery()->getTable()->getModel()->getName());

			foreach ($_array = $src_field->getPhysical() as $_field)
				$_physicstts[0][] = $this->aliasedField($bridgealias, $_field) . ' = ' . 
					  $this->aliasedField($this->getTargetQuery()->getAlias(), $_field->getReferencedField());

			$target_field = $bridge->getModel()->getField($this->getSrcQuery()->getTable()->getModel()->getName());
			echo $src_field, $target_field;

			foreach ($_array = $target_field->getPhysical() as $_field)
				$_physicstts[1][] = $this->getSrcQuery()->getAlias() . '.' . $_field->getReferencedField()->getName() . ' = ' . 
					  $bridgealias . '.' . $_field->getName();

			return sprintf(' LEFT JOIN %s ON (%s) LEFT JOIN %s ON (%s) ', 
					  $this->aliasedTableDefinition($bridge, $bridgealias), implode(' AND ', $_physicstts[0]), 
					  $this->getSrcQuery()->aliasedTableName(), implode(' AND ', $_physicstts[1]));
		}
		else {
			$_physic    = $this->getField()->getPhysical();
			
			if ($this->getType() == QueryJoin::LEFT_JOIN) {
				foreach ($_physic as $_field)
					$_physicstt[] = $this->getSrcQuery()->getAlias() . '.' . $_field->getName() . ' = ' . 
						  $this->getTargetQuery ()->getAlias() . '.' . $_field->getReferencedField()->getName();
			
				return sprintf(' LEFT JOIN %s ON(%s) ', $this->getSrcQuery()->aliasedTableName(), implode(' AND ', $_physicstt));
			}
			else {
				foreach ($_physic as $_field)
					$_physicstt[] = $this->getTargetQuery()->getAlias() . '.' . $_field->getName() . ' = ' . 
						  $this->getSrcQuery()->getAlias() . '.' . $_field->getReferencedField()->getName();
				return sprintf(' LEFT JOIN %s ON(%s) ', $this->getSrcQuery()->aliasedTableName(), implode(' AND ', $_physicstt));
			}
		}
	}
	
}