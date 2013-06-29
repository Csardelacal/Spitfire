<?php

namespace spitfire\storage\database;

use spitfire\model\Reference;
use \Model;

/**
 * An ancestor is an element that defines a descending relation between two 
 * database tables. This is important when two Models / Tables are connected in
 * several ways.
 * 
 * Every ancestor is defined by the Record that holds information about it and
 * the reference that allows us to check what relation exists between them.
 * 
 * Ancestors are best explained with the example their named after, parents. 
 * A baby descends from a father and a mother. The baby's relation to his father 
 * is 'father' and the relation to his mother, 'mother'. Opposed to this, the
 * baby is a 'child' of father and mother so the relation offers no information
 * in the opposite way.
 * 
 * In our example the Ancestor class contains the father and the relation 'father'
 * so we can retrieve the baby by looking for babies whose father is our ancestor.
 * Obviously this is not a perfect example, as no person can be a father and a 
 * mother at the same time. A better example would be a car inssurance were the
 * one who pays the insurance and the driver are not always the same person.
 * 
 * @author César de la Cal <cesar@magic3w.com>
 * @last-revision 2013-05-17
 */
class Ancestor
{
	/**
	 * This object is the reference the child model has with the parent 
	 * object. From this it is easy to retrieve the ends of the relation
	 * or the name of the role the parent plays.
	 * @var Reference 
	 */
	private $relation;
	
	/**
	 * This object is the 'payload' it is the actual ancestor of the elements
	 * related to this.
	 * @var Model
	 */
	private $parent;
	
	/**
	 * Creates a new ancestor. This allows to create a descending connection
	 * between database tables.
	 * 
	 * @param Model $parent
	 * @param \spitfire\model\Reference|Reference $relation
	 */
	public function __construct(Model$parent, Reference$relation) {
		$this->parent   = $parent;
		$this->relation = $relation;
	}
	
	/**
	 * Sets the relation the ancestor has to any of it's children. Child base
	 * class (model) can be retrieved by using ->getRelation()->getSource().
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
	
	/**
	 * Get the parent element. This is the base of the ancestor itself, 
	 * without any direct reference to it's child.
	 * 
	 * @return Model
	 */
	public function getParent() {
		return $this->parent;
	}
	
	/**
	 * Define the base element of the ancestor.
	 * 
	 * @param Model $parent
	 */
	public function setParent(Model$parent) {
		$this->parent = $parent;
	}
}