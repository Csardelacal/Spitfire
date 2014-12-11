<?php

namespace spitfire\storage\database\drivers;

use spitfire\storage\database\Query;
use spitfire\storage\database\QueryField;

class MysqlPDOQuery extends Query
{
	public function execute($fields = null) {
		
		$this->setAliased(false);
		
		#Declare vars
		$rpp          = $this->getResultsPerPage();
		$offset       = ($this->getPage() - 1) * $rpp;
		
		$selectstt    = 'SELECT';
		$fields       = implode(', ', ($fields)? $fields : $this->table->getTable()->getFields());
		$fromstt      = 'FROM';
		$tablename    = $this->aliasedTableName();
		$wherestt     = 'WHERE';
		/** @link http://www.spitfirephp.com/wiki/index.php/Database/subqueries Information about the filter*/
		$restrictions = array_filter($this->getRestrictions(), Array('spitfire\storage\database\Query', 'restrictionFilter'));
		$orderstt     = 'ORDER BY';
		$order        = $this->getOrder();
		$limitstt     = 'LIMIT';
		$limit        = $offset . ', ' . $rpp;
		
		
		#Import tables for restrictions from remote queries
		$subqueries = $this->getPhysicalSubqueries();
		$joins      = Array();
		
		foreach ($subqueries as $q) {
			$joins[] = sprintf('LEFT JOIN %s ON (%s)', $q->getQueryTable()->definition(), implode(' AND ', $q->getRestrictions()));
		}
		
		$join = implode(' ', $joins);
		
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
			$order = "{$order['field']} {$order['mode']}";
		}
		
		$stt = array_filter(Array( $selectstt, $fields, $fromstt, $tablename, $join, 
		    $wherestt, $restrictions, $orderstt, $order, $limitstt, $limit));
		
		return new mysqlPDOResultSet($this->getTable(), $this->getTable()->getDb()->execute(implode(' ', $stt)));
		
	}

	public function restrictionGroupInstance() {
		return new MysqlPDORestrictionGroup($this);
	}

	public function restrictionInstance(QueryField$field, $value, $operator) {
		return new MysqlPDORestriction($this, $field, $value, $operator);
	}

	public function aliasedTableName() {
		if ($this->getAliased()) return $this->getTable() . ' AS ' . $this->getAlias();
		else return $this->getTable();
	}

	public function queryFieldInstance($field) {
		if ($field instanceof QueryField) {return $field; }
		return new MysqlPDOQueryField($this, $field);
	}

	public function queryTableInstance(\spitfire\storage\database\Table $table) {
		return new MysqlPDOQueryTable($this, $table);
	}

	public function compositeRestrictionInstance(\spitfire\model\Field $field = null, $value, $operator) {
		return new MysqlPDOCompositeRestriction($this, $field, $value, $operator);
	}

	public function delete() {
		
		
		$this->setAliased(false);
		
		#Declare vars
		$rpp          = $this->getResultsPerPage();
		$offset       = ($this->getPage() - 1) * $rpp;
		
		$selectstt    = 'DELETE';
		$fromstt      = 'FROM';
		$tablename    = $this->aliasedTableName();
		$wherestt     = 'WHERE';
		/** @link http://www.spitfirephp.com/wiki/index.php/Database/subqueries Information about the filter*/
		$restrictions = array_filter($this->getRestrictions(), Array('spitfire\storage\database\Query', 'restrictionFilter'));
		
		
		#Import tables for restrictions from remote queries
		$subqueries = $this->getPhysicalSubqueries();
		$joins      = Array();
		
		foreach ($subqueries as $q) {
			$joins[] = sprintf('LEFT JOIN %s ON (%s)', $q->getQueryTable()->definition(), implode(' AND ', $q->getRestrictions()));
		}
		
		$join = implode(' ', $joins);
		
		#Restrictions
		if (empty($restrictions)) {
			$restrictions = '1';
		}
		else {
			$restrictions = implode(' AND ', $restrictions);
		}
		
		$stt = array_filter(Array( $selectstt, $fromstt, $tablename, $join, 
		    $wherestt, $restrictions));
		
		$this->getTable()->getDb()->execute(implode(' ', $stt));
	}
}