<?php

namespace spitfire\storage\database;

use spitfire\model\Field;
use \Model;

class CompositeRestriction
{
	/**
	 * The parent query for this restriction. This provides information about
	 * how it's table is currently aliased and what fields this table can provide.
	 *
	 * @var spitfire\storage\database\Query
	 */
	private $query;
	private $field;
	private $value;
	private $operator;
	
	public function __construct(Query$query, Field$field = null, $value = null, $operator = Restriction::EQUAL_OPERATOR) {
		$this->query = $query;
		$this->field = $field;
		$this->value = $value;
		$this->operator = $operator;
		
		if ($this->value instanceof Model) { $this->value = $this->value->getQuery(); }
		if ($this->value instanceof Query) { $this->value->setAliased(true); }
	}
	
	/**
	 * 
	 * @return Query
	 */
	public function getQuery() {
		return $this->query;
	}

	public function setQuery(Query$query) {
		$this->query = $query;
	}

	public function getField() {
		return $this->field;
	}

	public function setField(Field$field) {
		$this->field = $field;
	}

	public function getValue() {
		if ($this->value instanceof Model) { $this->value = $this->value->getQuery(); }
		return $this->value;
	}

	public function setValue($value) {
		$this->value = $value;
	}

	public function getOperator() {
		return $this->operator === null? '=' : $this->operator;
	}

	public function setOperator($operator) {
		$this->operator = $operator;
	}
	
	/**
	 * This method handles NULL scenarios.
	 * 
	 * This method simplifies complex restrictions when null values are involved.
	 * Usually, when querying you will define an equivalence between two values and
	 * launch the query. This method is called when that involves null.
	 * 
	 * You can either have a null value, which will force the database to check that
	 * the physical fields composing your logical field are null.
	 * 
	 * Or you can have a null field. Which will force the database to check that 
	 * one of the fields that this table has equals to the value you specified.
	 * 
	 * Please note that the usage of this function for other scenarios has been
	 * deprecated since 11/2014
	 * 
	 * 
	 * @return type
	 */
	public function getSimpleRestrictions() {
		if ($this->field === null) {
			$table = $this->getQuery()->getTable();
			$fields = $table->getFields();
			$restrictions = $this->query->restrictionGroupInstance();
			
			foreach ($fields as $field) {
				if (!$field->getLogicalField() instanceof \Reference) {
					$restrictions->addRestriction($field, $this->getValue(), $this->operator);
				}
			}
			return Array($restrictions);
		}
		
		if ($this->value === null) {
			$restrictions = Array();
			foreach ($fields = $this->getField()->getPhysical() as $field) {
				$f = $this->getQuery()->queryFieldInstance($field);
				$v = null;
				$r = $this->getQuery()->restrictionInstance($f, $v, 'IS');
				$restrictions[] = $r;
			}
			return $restrictions;
		}
		
		trigger_error('Deprecated method getSimpleRestrictions was used', E_USER_DEPRECATED);
		if ($this->value instanceof Model) 
			$this->value = $this->value->getQuery();
		
		if ($this->value instanceof Query)
			return $this->value->getRestrictions();
	}
	
	
	public function getPhysicalSubqueries() {
		if ($this->field === null || $this->value === null) { return Array(); }
		
		$field     = $this->getField();
		$connector = $field->getConnectorQueries($this->getQuery());
		$last      = end($connector);
		
		$last->setId($this->getValue()->getId());
		$last->importRestrictions($this->getValue());
		
		/*
		 * In case of the field being a childrenfield, we need to get the connector
		 * from the first subquery and plug it into the current one.
		 * 
		 * Basically, since a childrenfield works exactly the other way around that
		 * a normal reference does we need to turn the restrictions that the reference
		 * would normally have pointing at it's parent into the childrenfield's 
		 * restrictions.
		 * 
		 * This currently causes a redundant restrictions to appear, but these shouldn't
		 * harm the operation as it is.
		 */
		if ($this->field instanceof \ChildrenField) { 
			$subqueries = $last->getPhysicalSubqueries();
			$first      = reset($subqueries);
			
			foreach ($first->getRestrictions() as $r) { 
				$v = $r->getField(); 
				$q1 = $v->getQuery();
				$q2 = $last;
				if ($v instanceof QueryField && $q1 === $q2) {
					$last->addRestriction($r->getField(), $r->getValue());
					$first->removeRestriction($r);
				}
			}
			
			return array_merge($subqueries, $connector); 
		}
		
		return array_merge($last->getPhysicalSubqueries(), $connector);
	}
	
}