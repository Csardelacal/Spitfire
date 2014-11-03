<?php

namespace spitfire\storage\database;

/**
 * A restriction group contains a set of restrictions (or restriction groups)
 * that can be used by the database to generate more complex queries.
 * 
 * This groups can be diferent of two different types, they can be 'OR' or 'AND',
 * changing the behavior of the group by making it more or less restrictive. This
 * OR and AND types are known from most DBMS.
 */
abstract class RestrictionGroup
{
	const TYPE_OR  = 'OR';
	const TYPE_AND = 'AND';
	
	private $restrictions;
	private $belongsto;
	private $type = self::TYPE_OR;
	
	public function __construct(Query$belongsto = null, $restrictions = Array() ) {
		$this->belongsto    = $belongsto;
		$this->restrictions = $restrictions;
	}
	
	public function putRestriction($restriction) {
		$this->restrictions[] = $restriction;
	}
	
	public function setRestrictions($restrictions) {
		$this->restrictions = $restrictions;
	}
	
	public function addRestriction($fieldname, $value, $operator = null) {
		try {
			#If the name of the field passed is a physical field we just use it to 
			#get a queryField
			if ($fieldname instanceof QueryField) {$field = $fieldname;}
			else { $field = $this->belongsto->queryFieldInstance($this->belongsto->getTable()->getField($fieldname)); }
			$restriction = $this->belongsto->restrictionInstance($field, $value, $operator);
			
		} catch (\Exception $e) {
			#Otherwise we create a complex restriction for a logical field.
			$field = $this->belongsto->getTable()->getModel()->getField($fieldname);
			
			if ($fieldname instanceof \Reference && $fieldname->getTarget() === $this->belongsto->getTable()->getModel())
				$field = $fieldname;
			
			$restriction = $this->belongsto->compositeRestrictionInstance($field, $value, $operator);
		}
		
		$this->restrictions[] = $restriction;
		return $this;
	}
	
	public function getRestrictions() {
		return $this->restrictions;
	}
	
	public function getRestriction($index) {
		return $this->restrictions[$index];
	}
	
	public function getCompositeRestrictions() {
		$_return = Array();
		foreach ($this->restrictions as $restriction) {
			if ($restriction instanceof CompositeRestriction) {
				$_return[] = $restriction;
				$_return = array_merge($_return, $restriction->getValue()->getCompositeRestrictions());
			} 
			if ($restriction instanceof RestrictionGroup) {
				$_return = array_merge($_return, $restriction->getCompositeRestrictions());
			}
		}
		return $_return;
	}
	
	public function getValues() {
		$values = Array();
		foreach ($this->restrictions as $r) $values[] = $r->getValue();
		return $values;
	}
	
	public function group() {
		return $this->restrictions[] = $this->belongsto->restrictionGroupInstance();
	}
	
	public function endGroup() {
		return $this->belongsto;
	}
	
	public function getJoins() {
		$_joins = Array();
		
		foreach($this->restrictions as $restriction) {
			$_joins = array_merge($_joins, $restriction->getJoins());
		}
		
		return $_joins;
	}
	
	public function setQuery(Query$query) {
		$this->belongsto = $query;
		
		foreach ($this->restrictions as $restriction) { $restriction->setQuery($query);}
	}
	
	public function getQuery() {
		return $this->belongsto;
	}
	
	public function setType($type) {
		if ($type === self::TYPE_AND || $type === self::TYPE_OR) {
			$this->type = $type;
			return $this;
		}
		else {
			throw new \InvalidArgumentException("Restriction groups can only be of type AND or OR");
		}
	}
	
	public function getType() {
		return $this->type;
	}
	
	public function getPhysicalSubqueries() {
		$_ret = Array();
		foreach ($this->getRestrictions() as $r) {
			array_merge($_ret, $r->getPhysicalSubqueries());
		}
		
		return $_ret;
	}
	
	public function __clone() {
		$newr = Array();
		foreach ($this->restrictions as $r) { $newr[] = clone $r; }
	}

	abstract public function __toString();
}