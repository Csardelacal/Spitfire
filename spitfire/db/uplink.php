<?php

namespace spitfire\storage\database;

use Reference;

/**
 * An uplink is an element that defines a relation between two database
 * queries. This is useful when we create a complex restriction and need to 
 * create a set of restrictions that connect the tables.
 * 
 * Every uplink is defined by two queries being connected and a reference
 * that defines what field is used to connect them and allows us to check what 
 * relation exists between them.
 * 
 * This class is a helper that removes the need to create loops through the 
 * physical fields of a restriction and generate clean code in the calling methods.
 * 
 * For readability, encapsulation and code cleanness the only type of connecting 
 * field accpeted is actually a reference. This forces the code that uses uplinks
 * to convert the other types to References and use those.
 * 
 * 
 * @deprecated since version 0.1-dev.20141111
 * @author César de la Cal <cesar@magic3w.com>
 * @last-revision 2013-09-30
 */
class Uplink
{
	/**
	 * This object is the reference the child model has with the parent 
	 * object. From this it is easy to retrieve the ends of the relation
	 * or the name of the role the parent plays.
	 * @var Reference 
	 */
	private $relation;
	
	/**
	 * This is the source query. The source query is the owner of the relation
	 * reference field and therefore the one that 'points' to the other query.
	 * 
	 * @var spitfire\storage\database\Query
	 */
	private $src;
	
	/**
	 * This query is the target. It is the one being 'pointed at'.
	 * 
	 * @var spitfire\storage\database\Query
	 */
	private $target;
	
	/**
	 * Creates a new uplink. This helps connecting two queries and receiving the
	 * restrictions needed to connect them.
	 * 
	 * @param spitfire\storage\database\Query $src
	 * @param spitfire\storage\database\Query $target
	 * @param spitfire\model\Reference $relation
	 */
	public function __construct(Query$src, Query$target, Reference$relation) {
		$this->src      = $src;
		$this->target   = $target;
		$this->relation = $relation;
	}
	
	/**
	 * Returns the field that is used to connect both queries.
	 * 
	 * @return Reference
	 */
	public function getRelation() {
		return $this->relation;
	}
	
	/**
	 * Sets the relation between the child and the ancestor so you can 
	 * retrieve both of their base classes and the fields they share from
	 * this object at a later point.
	 * 
	 * @param Reference $relation
	 */
	public function setRelation($relation) {
		$this->relation = $relation;
	}
	
	public function getSrc() {
		return $this->src;
	}

	public function getTarget() {
		return $this->target;
	}

	public function setSrc(Query $src) {
		$this->src = $src;
	}

	public function setTarget(Query $target) {
		$this->target = $target;
	}
	
	public function getRestrictions() {
		$group    = $this->target->restrictionGroupInstance()->setType(RestrictionGroup::TYPE_AND);
		$physical = $this->relation->getPhysical();
		
		foreach ($physical as $field) {
			$group->putRestriction(
				$this->getSrc()->restrictionInstance(//Refers to MySQL
					$this->src->queryFieldInstance($field), //This two can be put in any order
					$this->target->queryFieldInstance($field->getReferencedField()), 
					Restriction::EQUAL_OPERATOR
				 )
			);
		}
		
		return $group;
	}

}