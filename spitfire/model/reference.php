<?php

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
	 * This is the name the source model (alas. the one referencing the target)
	 * will use to reference the content of this relation.
	 * @var string
	 */
	private $alias;
	
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
	public function __construct(Model$source, Model$target, $alias = null) {
		$this->source = $source;
		$this->target = $target;
		$this->alias  = $alias;
	}
}