<?php

namespace spitfire\storage\database;

class RestrictionGroup
{
	private $restrictions;
	private $belongsto;
	
	private $strigifyCallback;
	
	public function __construct(Query$belongsto = null, $restrictions = Array() ) {
		$this->belongsto    = $belongsto;
		$this->restrictions = $restrictions;
	}
	
	public function addRestriction($fieldname, $value, $operator = null) {
		
		$field = $this->belongsto->getTable()->getField($fieldname);
		$this->restrictions[] = new Restriction($field, $value, $operator);
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
	
	public function __toString() {
		return implode(' AND ', $this->restrictions);
	}
}