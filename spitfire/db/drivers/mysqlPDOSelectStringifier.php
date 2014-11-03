<?php namespace spitfire\storage\database\drivers;

use spitfire\storage\database\Query;
use spitfire\storage\database\CompositeRestriction;
use spitfire\storage\database\Restriction;
use spitfire\storage\database\RestrictionGroup;

class mysqlPDOSelectStringifier
{
	
	private $query;
	private $parent;
	
	private $groups;
	private $composite;
	private $simple;
	
	public function __construct(Query$query, Query$parent = null) {
		$this->query  = $query;
		$this->parent = $parent;
		
		$restrictions = $query->getRestrictions();
		foreach ($restrictions as $restriction) {
			if ($restriction instanceof CompositeRestriction) {$this->composite[] = $restriction;}
			elseif ($restriction instanceof RestrictionGroup) {$this->groups[] = $restriction;}
			elseif ($restriction instanceof Restriction)      {$this->simple[] = $restriction;}
		}
	}
	
	public function stringifyQuery() {
		$pieces = Array('SELECT', $fields, 'FROM', $table, $joins, 'WHERE', $restrictions);
		return implode(' ', array_filter($pieces));
	}
	
	public function stringifySubQuery() {
		
	}
	
	public function __toString() {
		return ($this->parent === null)? $this->stringifyQuery() : $this->stringifySubQuery();
	}

	
}