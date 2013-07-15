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
			
			$restriction_list = Array();
			foreach ($restrictions as $restriction) {
				if ($restriction instanceof Restriction && $restriction->getValue() instanceof Query)
					$restriction_list[] = $restriction;
				
				elseif ($restriction instanceof RestrictionGroup)
					foreach($r = $restriction->getRestrictions() as $restr) {
						if ($restr instanceof Restriction && $restr->getValue() instanceof Query)
							$restriction_list[] = $restr;
					}
			}
			
			foreach ($restriction_list as $restriction) {
				$value = $restriction->getValue();
				#Check if the table needs to be aliased
				if (in_array($value->getTable(), $used_tables)) $value->setAliased(true);
				else $used_tables[] = $value->getTable();
				
				#
				if ($restriction->getField()->getTable() === $this->getTable() ) {
					$local_f      = $restriction->getField()->getPhysical();
					$local_alias  = $restriction->getQuery()->getAlias();
					$remote_alias = $restriction->getValue()->getAlias();
					
					foreach($local_f as $field)
						$remote_f[]  = $field->getReferencedField();
				}
				else {
					$local_f      = $restriction->getField()->getPhysical();
					$local_alias  = $restriction->getValue()->getAlias();
					$remote_alias = $restriction->getQuery()->getAlias();
					
					foreach($local_f as $field)
						$remote_f[]  = $field->getReferencedField();
				}

				$remote_p  = Array();
				
				foreach($local_f as $index => $field) 
						$remote_p[] = "{$local_alias}.{$field->getName()} = {$remote_alias}.{$remote_f[$index]->getName()}";

				$join.= 'LEFT JOIN ' . $value->aliasedTableName();
				$join.= ' ON (' . implode(' AND ', $remote_p) . ') ';
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