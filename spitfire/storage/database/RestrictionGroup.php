<?php namespace spitfire\storage\database;

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
	private $parent;
	private $type = self::TYPE_OR;
	
	public function __construct(RestrictionGroup$parent = null, $restrictions = Array() ) {
		$this->parent       = $parent;
		$this->restrictions = $restrictions;
	}
	
	public function removeRestriction($r) {
		unset($this->restrictions[array_search($r, $this->restrictions)]);
	}
	
	public function putRestriction($restriction) {
		$this->restrictions[] = $restriction;
	}
	
	public function setRestrictions($restrictions) {
		$this->restrictions = $restrictions;
	}
	
	/**
	 * Adds a restriction to the current query. Restraining the data a field
	 * in it can contain.
	 * 
	 * @see http://www.spitfirephp.com/wiki/index.php/Method:spitfire/storage/database/Query::addRestriction
	 * @param string $fieldname
	 * @param mixed  $value
	 * @param string $operator
	 * @return spitfire\storage\database\RestrictionGroup
	 */
	public function addRestriction($fieldname, $value, $operator = '=') {
		
		try {
			#If the name of the field passed is a physical field we just use it to 
			#get a queryField
			$field = $fieldname instanceof QueryField? $fieldname : $this->table->getTable()->getField($fieldname);
			$restriction = $this->restrictionInstance($this->queryFieldInstance($field), $value, $operator);
			
		} catch (\Exception $e) {
			#Otherwise we create a complex restriction for a logical field.
			$field = $this->getQuery()->getTable()->getModel()->getField($fieldname);
			
			if ($fieldname instanceof \Reference && $fieldname->getTarget() === $this->table->getModel())
			{ $field = $fieldname; }
			
			#If the fieldname was not null, but the field is null - it means that the system could not find the field and is kicking back
			if ($field === null && $fieldname!== null) { throw new \spitfire\exceptions\PrivateException('No field ' . $fieldname, 201602231949); }
			
			$restriction = $this->compositeRestrictionInstance($field, $value, $operator);
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
	
	public function getConnectingRestrictions() {
		$_ret = Array();
		
		foreach ($this->restrictions as $r) { $_ret = array_merge($_ret, $r->getConnectingRestrictions());}
		
		return $_ret;
	}
	
	public function group() {
		return $this->restrictions[] = $this->parent->restrictionGroupInstance();
	}
	
	public function endGroup() {
		return $this->parent;
	}
	
	public function setQuery(Query$query) {
		$this->parent = $query;
		
		foreach ($this->restrictions as $restriction) { $restriction->setQuery($query);}
	}
	
	public function getParent() {
		return $this->parent;
	}
	
	/**
	 * As opposed to the getParent method, the getQuery method will ensure that
	 * the return is a query.
	 * 
	 * This allows the application to quickly get information about the query even
	 * if the restrictions are inside of several layers of restriction groups.
	 * 
	 * @return Query
	 */
	public function getQuery() {
		return $this->parent->getQuery();
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
			$_ret = array_merge($_ret, $r->getPhysicalSubqueries());
		}
		
		return $_ret;
	}
	
	public function __clone() {
		/*@var $newr Restriction[] */
		$newr = Array();
		
		foreach ($this->restrictions as $r) { 
			$newr[] = clone $r; 
		}
	}

	abstract public function __toString();
}