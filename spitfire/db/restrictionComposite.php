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
		
		if ($this->field instanceof \ChildrenField) { return array_merge($connector, $last->getPhysicalSubqueries()); }
		return array_merge($last->getPhysicalSubqueries(), $connector);
	}
	
	/**
	 * 
	 * @deprecated since version 0.1dev
	 * @return type
	 */
	public function getConnectingRestrictions() {
		
		if ($this->field === null) { return null; }
		
		if ($this->field instanceof \Reference && $this->field->getTable() === $this->getQuery()->getTable()) {
			$uplink = new Uplink($this->getQuery(), $this->getValue(), $this->field);
			return Array($uplink->getRestrictions());
			
		}
		
		elseif ($this->field instanceof \ManyToManyField && $this->field->getTable() === $this->value->getTable()) {
			$route1       = $this->field->getBridge()->getTable()->getQueryInstance();
			$route1->setAliased(true);
			$route2       = $this->field->getBridge()->getTable()->getQueryInstance();
			$route2->setAliased(true);
			$fields       = $this->field->getBridge()->getFields();
			
			$restrictions = Array();
			
			//Route #1
			$uplink1 = new Downlink($route1, $this->getQuery(), reset($fields));
			$restrictions[0] = $uplink1->getRestrictions();
			
			//Route2
			$uplink2 = new Downlink($route2, $this->getQuery(), end($fields));
			$restrictions[1] = $uplink2->getRestrictions();
			
			//Merge
			$uplink3 = new Uplink($route1, $this->getValue(), end($fields));
			$uplink4 = new Uplink($route2, $this->getValue(), reset($fields));
			$restrictions[2] = $this->getValue()->restrictionGroupInstance();
			$restrictions[2]->putRestriction($uplink3->getRestrictions());
			$restrictions[2]->putRestriction($uplink4->getRestrictions());
			
			return $restrictions;
			
			
		}
		
		elseif ($this->field instanceof \ManyToManyField) {
			$subquery     = $this->field->getBridge()->getTable()->getQueryInstance();
			$subquery->setAliased(true);
			$fields       = $this->field->getBridge()->getFields();
			
			foreach ($fields as $field) {
				
				if ($field->getTarget() === $this->getQuery()->getTable()->getModel()) {
					$link2 = new Downlink($subquery, $this->getQuery(), $field);
					$restrictions[0] = $link2->getRestrictions();
				}
				if ($field->getTarget() === $this->value->getTable()->getModel()) {
					$link2 = new Uplink($subquery, $this->value, $field);
					$restrictions[1] = $link2->getRestrictions();
				}
					
			}
			
			return Array($restrictions[0], $restrictions[1]);
			
		}
		
		elseif ($this->field instanceof \ChildrenField ||
				  $this->field instanceof \Reference && $this->field->getTable() !== $this->getQuery()->getTable()) {

			if ($this->field instanceof \ChildrenField) {
				$field = $this->field->findReference();
			}
			else {
				$field = $this->field;
			}
			
			$uplink = new Downlink($this->getValue(), $this->getQuery(), $field);
			return Array($uplink->getRestrictions());
		}
	}

}