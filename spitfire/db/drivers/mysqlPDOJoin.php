<?php

namespace spitfire\storage\database\drivers;

use \Exception;
use spitfire\storage\database\Restriction;
use \spitfire\storage\database\QueryJoin;
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
			

			foreach ($restrictions as $simple_restrictions) {
				
				if ($simple_restrictions instanceof \spitfire\storage\database\RestrictionGroup) {
			
					$table = $simple_restrictions->getQuery()->getQueryTable();
					
					$_return.= sprintf("LEFT JOIN %s ON %s", $table->definition(), $simple_restrictions);
				}
				else {
					$table = (reset($simple_restrictions) instanceof Restriction)? 
							  reset($simple_restrictions)->getValue()->getQuery()->getQueryTable() : 
								reset($simple_restrictions)->getRestriction(0)->getField()->getQuery()->getQueryTable();

					$_return.= sprintf("LEFT JOIN %s ON (%s)", 
							  $table->definition(),
							  implode(' AND ', $simple_restrictions));
				}
			}

			return $_return;
		}
		catch(Exception $e) {
			echo $e->getMessage() . "\n";
			echo $e->getTraceAsString();
			die();
		}
		
		/*
		if ($this->restriction->getField() instanceof \ManyToManyField) {
			$restrictions = $this->restriction->getConnectingRestrictions();
			$query        = $this->restriction->getQuery();
			$bridge_restr = Array();
			
			
			foreach ($restrictions as $index => $r) {
				
				if ($r->getField()->getQuery() === $query ||
					 $r->getValue()->getQuery() === $query) {
					$bridge_restr[] = $r;
					unset($restrictions[$index]);
				}
			}
			
			return sprintf("LEFT JOIN %s ON (%s) LEFT JOIN %s ON (%s)", 
					  reset($bridge_restr)->getValue()->getQuery()->getQueryTable()->definition(),
					  implode(' AND ', $bridge_restr),
					  $this->restriction->getValue()->getQueryTable()->definition(),
					  implode(' AND ', $restrictions));
		}
		else {
			return sprintf("LEFT JOIN %s ON (%s)", 
					  $this->restriction->getValue()->getQueryTable()->definition(),
					  implode(' AND ', $this->restriction->getConnectingRestrictions()));
		}/**/
		
	}
	
	public function aliasedTableDefinition(Table$table, $alias) {
		return sprintf('%s AS `%s`', $table , $alias);
	}
	
	public function aliasedField($tableAlias, DBField$field) {
		return sprintf('`%s`.`%s`', $tableAlias, $field->getName());
	}
	
	public function __2toString() {
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