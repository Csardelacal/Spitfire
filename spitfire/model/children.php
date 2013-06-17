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
	
	public function getTarget() {
		#Check if the passed argument already is a model
		if ($this->target instanceof Model) {
			return $this->target;
		} 
		elseif ($this->target === $this->getModel()->getName()) {
			return $this->target = $this->getModel();
		}
		else {
			return $this->target = $this->getModel()->getTable()->getDB()->table($this->target)->getModel();
		}
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