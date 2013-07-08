<?php

use spitfire\model\Field;

class ChildrenField extends Field
{
	private $role;
	private $target;
	
	public function __construct($target, $role) {
		$this->target = $target;
		$this->role   = $role;
	}
	
	/**
	 * Returns the model this fields is pointing to, the child model. It is referred
	 * as target due to the fact that this field is pointing at it.
	 * 
	 * @return Schema
	 */
	public function getTarget() {
		#Check if the passed argument already is a model
		if ($this->target instanceof Schema) {
			return $this->target;
		} 
		elseif ($this->target === $this->getModel()->getName()) {
			return $this->target = $this->getModel();
		}
		else {
			return $this->target = $this->getModel()->getTable()->getDB()->table($this->target)->getModel();
		}
	}
	
	public function getReferencedFields() {
		$_return = Array();
		if (is_array($this->role))
		foreach ($this->role as $role) {
			$_return[] = $this->getTarget()->getField($role);
		}
		else {
			$_return[] = $this->getTarget()->getField($this->role);
		}
		
		return $_return;
	}
	
	public function getRole() {
		return $this->role;
	}
	
	public function getPhysical() {
		return Array();
	}

	public function getDataType() {
		return Field::TYPE_CHILDREN;
	}
	
}