<?php

namespace spitfire\storage\database\drivers;

use spitfire\storage\database\Query;
use spitfire\storage\database\Restriction;

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
		
		#Join specific tasks
		if ($_join) {
			$rem_table = $_join->getParent()->getTable();
			$remotef   = $_join->getParent()->getUniqueFields();
			$_remotef  = Array();
			
			foreach ($remotef as $f) {
				$model = $rem_table->getModel();
				$local_field = $this->table->getField("{$_join->getRelation()->getRole()}_{$f->getName()}");
				if ($local_field)
					$_remotef[] = "$f = $local_field";
			}
			
			$join = 'LEFT JOIN ' . $rem_table->getTableName();
			$join.= ' ON (' . implode(' AND ', $_remotef) . ')';
			$restrictions = array_merge($restrictions, $_join->getParent()->getUniqueRestrictions());
		}
		
		#Import tables for restrictions
		if (!empty($restrictions)) {
			foreach ($restrictions as $restriction) {
				if ( $restriction instanceof Restriction && ($value = $restriction->getValue()) instanceof Query) {
					$rem_table = $value->getTable();
					$remote_f  = $restriction->getField()->getReferencedFields();
					$remote_p  = Array();
					
					foreach($remote_f as $field) {
						$physical = $field->getPhysical();
						foreach ($physical as $phys) {
							$remote_p[] = "$phys = {$phys->getReferencedField()}";
						} 
					}
					
					$join.= 'LEFT JOIN ' . $rem_table->getTableName();
					$join.= ' ON (' . implode(' AND ', $remote_p) . ')';
				}
			}
		}
		
		#Restrictions
		if ( null != ($p = $this->getParent()) ) {
			$restrictions = array_merge($restrictions, $p->getParent()->getUniqueRestrictions());
		}
		
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
		return new MysqlPDORestriction($field, $value, $operator);
	}
}