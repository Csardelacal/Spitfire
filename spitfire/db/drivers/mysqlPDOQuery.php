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
		$fields       = ($fields)? $fields : $this->table->getTable()->getFields();
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
			$join  = implode($joins, ' ');
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

	public function restrictionInstance(QueryField$field, $value, $operator) {
		return new MysqlPDORestriction($this, $field, $value, $operator);
	}

	public function aliasedTableName() {
		if ($this->getAliased()) return $this->getTable() . ' AS ' . $this->getAlias();
		else return $this->getTable();
	}

	public function queryFieldInstance($field) {
		return new MysqlPDOQueryField($this, $field);
	}

	public function queryTableInstance(\spitfire\storage\database\Table $table) {
		return new MysqlPDOQueryTable($this, $table);
	}
}