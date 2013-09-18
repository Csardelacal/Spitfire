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
	
	public function __construct(Query$query, Field$field, $value, $operator = Restriction::EQUAL_OPERATOR) {
		$this->query = $query;
		$this->field = $field;
		$this->value = $value;
		$this->operator = $operator;
		
		if ($this->value instanceof Model)
			$this->value = $this->value->getQuery();
		
		if ($this->value instanceof Query)
			$this->value->setAliased(true);
	}
	
	/**
	 * 
	 * @return spitfire\storage\database\Query
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
		return $this->value;
	}

	public function setValue($value) {
		$this->value = $value;
	}

	public function getOperator() {
		return $this->operator;
	}

	public function setOperator($operator) {
		$this->operator = $operator;
	}
	
	public function getSimpleRestrictions() {
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
			
			
		if ($this->value instanceof Model) 
			$this->value = $this->value->getQuery();
		
		if ($this->value instanceof Query)
			return $this->value->getRestrictions();
	}
	
	public function getConnectingRestrictions() {
		
		$restrictions = Array();
		
		if ($this->field instanceof \Reference && $this->field->getTable() === $this->getQuery()->getTable()) {
			$physical     = $this->field->getPhysical();

			foreach($physical as $field) {
				$restrictions[] = $this->getQuery()->restrictionInstance(//Refers to MySQL
						  $this->query->queryFieldInstance($field), //This two can be put in any order
						  $this->value->queryFieldInstance($field->getReferencedField()), 
						  $this->operator);
			}

			return $restrictions;
		}
		
		elseif ($this->field instanceof \ManyToManyField) {
			$subquery     = $this->field->getBridge()->getTable()->getQueryInstance();
			$subquery->setAliased(true);
			$fields       = $this->field->getBridge()->getFields();
			
			foreach ($fields as $field) {
				$physical = $field->getPhysical();
				foreach ($physical as $ph) {
					if ($field->getTarget() === $this->getQuery()->getTable()->getModel()) {
						$restrictions[] = $this->getQuery()->restrictionInstance(
								  $this->query->queryFieldInstance($ph->getReferencedField()),
								  $subquery->queryFieldInstance($ph),
								  $this->operator);
					}
					if ($this->value->getTable()->getModel() === $this->getQuery()->getTable()->getModel()) {
						$restrictions[] = $this->getQuery()->restrictionInstance(
								  $this->value->queryFieldInstance($ph->getReferencedField()),
								  $subquery->queryFieldInstance($ph),
								  $this->operator);
					}
				}
			}
			
			return $restrictions;
			
		}
		
		elseif ($this->field instanceof \ChildrenField ||
				  $this->field instanceof \Reference && $this->field->getTable() !== $this->getQuery()->getTable()) {
			$physical     = $this->field->findReference()->getPhysical();

			foreach($physical as $field) {
				$restrictions[] = $this->getQuery()->restrictionInstance(//Refers to MySQL
						  $this->query->queryFieldInstance($field->getReferencedField()), //This two can be put in any order
						  $this->value->queryFieldInstance($field), 
						  $this->operator);
			}

			return $restrictions;
		}
	}

}