<?php

namespace spitfire\storage\database\drivers;

use spitfire\storage\database\Query;

class MysqlPDOQuery extends Query
{
	public function execute($fields = null) {
		
		#Declare vars
		$rpp          = $this->getResultsPerPage();
		$offset       = ($this->getPage() - 1) * $rpp;
		$_join        = $this->getParent();
		
		$selectstt    = 'SELECT';
		$fields       = ($fields)? $fields : $this->table->getFields();
		$fromstt      = 'FROM';
		$tablename    = "`{$this->table->getTablename()}`";
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
		
		#Restrictions
		if ( null != ($p = $this->getParent()) ) {
			$restrictions = array_merge($restrictions, $p->getUniqueRestrictions());
		}
		
		if (empty($restrictions)) {
			$restrictions = '1';
		}
		else {
			$restrictions = implode(' AND ', $restrictions);
		}
		
		#Join specific tasks
		if ($_join && false) {//TODO: Repair
			$rem_table = $_join->getTable();
			$remotef   = $_join->getUniqueFields();
			$_remotef  = Array();
			$localf    = $table->getFields();
			foreach($localf as $f) {
				if (in_array($f->getReference(), $remotef) )
					$_remotef[] = $f . '=' . $f->getReference();
				else echo $f, ', ', $f->getReference();
			}
			
			foreach($remote as $r) $r->setStringify(Array($this, 'stringifyRestriction'));
			foreach($remotef as &$r) $r = $r->getName();
			
			$join = 'RIGHT JOIN ' . $rem_table->getTableName();
			$join.= ' ON (' . implode(', ', $_remotef) . ')';
			$restrictions = implode(' AND ', $remote) . " AND ($restrictions)";
		}
		
		if ($rpp < 0) {
			$limitstt = '';
			$limit    = '';
		}
		
		if (empty($order)) {
			$orderstt = '';
			$order    = '';
		}
		
		$stt = array_filter(Array( $selectstt, $fields, $fromstt, $tablename, $join, 
		    $wherestt, $restrictions, $orderstt, $order, $limitstt, $limit));
		
		return new mysqlPDOResultSet($this->getTable(), $this->getTable()->getDb()->execute(implode(' ', $stt)));
		
	}

	public function restrictionGroupInstance() {
		return new MysqlPDORestrictionGroup($this);
	}

	public function restrictionInstance($field, $value, $operator) {
		return new MysqlPDORestriction($field, $value, $operator);
	}
}