<?php

namespace spitfire\model;

use Model;

/**
 * Elements of this class indicate a connection between two Models, this allows
 * spitfire to let you use relational databases just like they were objects
 * (connected to each other). While real elements are scattered around tables
 * in no apparent manner.
 * 
 * This class doesn't indicate how the fields inside objects relate as their
 * names are automatically inherited by the source model when referencing. Names
 * for the fields are always given in a <i>alias</i>_<i>srcfield</i> fashion to 
 * improve understanding.
 * 
 * [NOTICE] The model doesn't check for the availability of field names, in case
 * you have duplicate field names due to references the system may behave in an 
 * unexpected way.
 * 
 * @author César de la cal <cesar@magic3w.com>
 * @last-revision 2013-05-16
 */
class Reference
{
	/**
	 * Indicates which model is the target of the reference. If model A 
	 * references model B, then B is the target.
	 * @var Model
	 */
	private $target;
	
	/**
	 * Indicates which model is the source of the reference. If model A 
	 * references model B, then A is the source.
	 * @var Model
	 */
	private $source;
	
	/**
	 * This is the role the target model will play inside the source, i.e.
	 * a person my be a friend or a boss for another, so you need to tell 
	 * Spitfire which one it is (only needed if several are available for you 
	 * to choose from) if none is specified it'll default to the target 
	 * model's name. It is also the name the source model (alas. the one 
	 * referencing the target) will use to reference the content of this 
	 * relation.
	 * 
	 * @var string
	 */
	private $role;
	
	/**
	 * Indicates if the fields inherited by the source model are primary to 
	 * it, this can be useful for relationships were 1:1 is required, or 
	 * where another field composes the primary key together with this one.
	 * @var boolean 
	 */
	private $primary;
	
	/**
	 * Creates a new reference between two models, this allows them to access
	 * data through the ORM, therefore not requiring additional user generated
	 * queries to retrieve data.
	 * 
	 * @param Model  $source
	 * @param Model  $target
	 * @param string $alias
	 */
	public function __construct(Model$source, Model$target, $role = null) {
		$this->source = $source;
		$this->target = $target;
		$this->role   = $role;
	}
	
	/**
	 * Defines the source model for this relation. Notice that this won't 
	 * magically include your relation into the new model. You've got to 
	 * manually add it to the references array within it.
	 * 
	 * @param Model $source
	 */
	public function setSource(Model$source) {
		$this->source = $source;
	}
	
	/**
	 * Returns the source of the relation (the model who is referencing the 
	 * other model). Allowing us to determine where the reference started.
	 * 
	 * @return Model
	 */
	public function getSource() {
		return $this->source;
	}
	
	/**
	 * Defines the target model, this model is the one 'donating' it's 
	 * primary key to the source so source can reference it as a parent
	 * model.
	 * 
	 * @param Model $target
	 */
	public function setTarget(Model$target) {
		$this->target = $target;
	}
	
	/**
	 * Returns the target model (the parent model of the source). Which offers
	 * it's primary keys to the target so it can reference them.
	 * 
	 * @return Model
	 */
	public function getTarget() {
		return $this->target;
	}
	
	/**
	 * Defines the role the target model plays inside the source model. By
	 * doing so you tell it how the elements should be named on the database,
	 * records and several other places.
	 * 
	 * @param string $role
	 */
	public function setRole($role) {
		$this->role = $role;
	}
	
	/**
	 * Returns the role the target plays inside the source. The role indicates
	 * the name of the target records inside the source records.
	 * 
	 * @return string
	 */
	public function getRole() {
		return $this->role;
	}
	
	/**
	 * Defines whether the fields inherited by this relationship should be 
	 * primary to the source model. This allows to define 1:1 and other
	 * special relations that may be useful.
	 * 
	 * @param boolean $primary
	 */
	public function setPrimary($primary) {
		$this->primary = !!$primary;
	}
	
	/**
	 * Reports whether the fields inherited by this relationship should be 
	 * considered primary or not by the source.
	 * 
	 * @return boolean
	 */
	public function getPrimary() {
		return $this->primary;
	}
	
	/**
	 * Returns a list of the fields this reference generates on the source
	 * Model. It copies the list of primary fields from the target model and
	 * prepends the role (separated by underscores) to the name of the field.
	 * 
	 * This function is just included for convenience. It just generates the
	 * data in a easy way to pick, so you cannot modify the way this is done
	 * or the fields below.
	 * 
	 * @return Field[]
	 */
	public function getFields() {
		
		$primary = $this->getTarget()->getPrimaryDBFields();
		$fields  = Array();
		
		foreach($primary as $field) {
			$ref   = $field;
			$field = clone $field;
			$name = $this->getRole() . '_' . $field->getName();
			$field->setName($name);
			$field->setPrimary($this->getPrimary());
			$field->setAutoIncrement(false);
			$field->setReference($this);
			$field->setReferencedField($ref);
			$fields[$name] = $field;
		}
		
		return $fields;
	}
}