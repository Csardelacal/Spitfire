<?php

namespace spitfire\storage\database\drivers;

use spitfire\storage\database\Query;
use spitfire\storage\database\Restriction;
use spitfire\storage\database\RestrictionGroup;

class MysqlPDOQuery extends Query
{
	public function execute($fields = null) {
		
		$this->setAliased(false);
		$used_tables = Array($this->getTable());
		
		#Declare vars
		$rpp          = $this->getResultsPerPage();
		$offset       = ($this->getPage() - 1) * $rpp;
		
		$selectstt    = 'SELECT';
		$fields       = ($fields)? $fields : $this->table->getFields();
		$fromstt      = 'FROM';
		$tablename    = $this->aliasedTableName();
		$join         = '';
		$wherestt     = 'WHERE';
		$restrictions = $this->getRestrictions();
		$orderstt     = 'ORDER BY';
		$order        = $this->getOrder();
		$limitstt     = 'LIMIT';
		$limit        = $offset . ', ' . $rpp;
		
		#Unset unneeded data & prepare for writing
		if (empty($fields)) {
			$fields = '*';
		}
		else {
			$fields = implode(', ', $fields);
		}
		
		#Import tables for restrictions from remote queries
		if (!empty($restrictions)) {
			
			$joins = $this->getJoins();
			
			foreach ($joins as $_join) {
				/* @var $_join \spitfire\storage\database\QueryJoin */
				
				if ($_join->getField() instanceof \ManyToManyField) {
					$bridge = $_join->getBridge();
					$br_fields = $bridge->getModel()->getFields();
					$_physicstts = Array();
					$bridgealias = 'table_'. rand();
					
					$_fields = array_shift($br_fields);
					
					foreach ($_array = $_fields->getPhysical() as $_field)
						$_physicstts[0][] = $bridgealias . '.' . $_field->getName() . ' = ' . 
							  $_join->getTargetQuery ()->getAlias() . '.' . $_field->getReferencedField()->getName();
					
					$_fields = array_shift($br_fields);
					
					foreach ($_array = $_fields->getPhysical() as $_field)
						$_physicstts[1][] = $_join->getSrcQuery()->getAlias() . '.' . $_field->getReferencedField()->getName() . ' = ' . 
							  $bridgealias . '.' . $_field->getName();
					
					$joinstt    = sprintf(' LEFT JOIN %s AS %s ON(%s) LEFT JOIN %s ON (%s) ', 
							  $bridge, $bridgealias, implode(' AND ', $_physicstts[0]), 
							  $_join->getSrcQuery()->aliasedTableName(), implode(' AND ', $_physicstts[1]));
					
				}
				else {
					$_physic    = $_join->getField()->getPhysical();
					
					foreach ($_physic as $_field)
						$_physicstt[] = $_join->getSrcQuery()->getAlias() . '.' . $_field->getName() . ' = ' . 
							  $_join->getTargetQuery ()->getAlias() . '.' . $_field->getReferencedField()->getName();
					
					$joinstt    = sprintf(' LEFT JOIN %s ON(%s) ', $_join->getSrcQuery()->aliasedTableName(), implode(' AND ', $_physicstt));
				}
				
				$join.= $joinstt;
			}
		}
		
		#Restrictions
		if (empty($restrictions)) {
			$restrictions = '1';
		}
		else {
			$restrictions = implode(' AND ', $restrictions);
		}
		
		if ($rpp < 0) {
			$limitstt = '';
			$limit    = '';
		}
		
		if (empty($order)) {
			$orderstt = '';
			$order    = '';
		}
		else {
			$order = "`{$order['field']}` {$order['mode']}";
		}
		
		$stt = array_filter(Array( $selectstt, $fields, $fromstt, $tablename, $join, 
		    $wherestt, $restrictions, $orderstt, $order, $limitstt, $limit));
		
		return new mysqlPDOResultSet($this->getTable(), $this->getTable()->getDb()->execute(implode(' ', $stt)));
		
	}

	public function restrictionGroupInstance() {
		return new MysqlPDORestrictionGroup($this);
	}

	public function restrictionInstance($field, $value, $operator) {
		return new MysqlPDORestriction($this, $field, $value, $operator);
	}

	public function aliasedTableName() {
		if ($this->getAliased()) return $this->getTable() . ' AS ' . $this->getAlias();
		else return $this->getTable();
	}
}