<?php

namespace spitfire\storage\database;

abstract class RestrictionGroup
{
	private $restrictions;
	private $belongsto;
	
	private $strigifyCallback;
	
	public function __construct(Query$belongsto = null, $restrictions = Array() ) {
		$this->belongsto    = $belongsto;
		$this->restrictions = $restrictions;
	}
	
	public function addRestriction($fieldname, $value, $operator = null) {
		try {
			#If the name of the field passed is a physical field we just use it to 
			#get a queryField
			$field = $this->belongsto->getTable()->getField($fieldname);
			$restriction = $this->belongsto->restrictionInstance($this->belongsto->queryFieldInstance($field), $value, $operator);
			
		} catch (\Exception $e) {
			#Otherwise we create a complex restriction for a logical field.
			$field = $this->belongsto->getTable()->getModel()->getField($fieldname);
			
			if ($fieldname instanceof \Reference && $fieldname->getTarget() === $this->belongsto->getTable()->getModel())
				$field = $fieldname;
			if ($field == null)
				throw new \privateException("No field '$fieldname'");
			
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
	
	public function getValues() {
		$values = Array();
		foreach ($this->restrictions as $r) $values[] = $r->getValue();
		return $values;
	}
	
	public function endGroup() {
		return $this->belongsto;
	}
	
	public function setStringify($callback) {
		$this->strigifyCallback = $callback;
		foreach ($this->restrictions as $r) $r->setStringify($callback);
	}
	
	public function getJoins() {
		$_joins = Array();
		
		foreach($this->restrictions as $restriction) {
			$_joins = array_merge($_joins, $restriction->getJoins());
		}
		
		return $_joins;
	}
	
	public function getQuery() {
		return $this->belongsto;
	}
	
	abstract public function __toString();
}