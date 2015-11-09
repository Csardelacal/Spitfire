<?php

use spitfire\model\Field;
use spitfire\Model;
use spitfire\model\adapters\ChildrenAdapter;

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
	
	public function getReferencedField() {
		if (!empty($this->role)) {
			return $this->getTarget()->getField($this->role);
		} else {
			$fields = $this->getTarget()->getFields();
			foreach ($fields as $field)
				if ($field instanceof \Reference && $field->getTarget() === $this)
					return $field;
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
	
	public function findReference() {
		$model = $this->getTarget();
		
		if ($model->getField($this->getRole())) return $model->getField($this->getRole());
		else {
			$fields = $model->getFields();
			foreach ($fields as $field) {
				if ($field instanceof Reference && $field->getTarget() === $this->getModel()) 
					return $field;
			}
		}
	}

	public function getAdapter(Model $model) {
		return new ChildrenAdapter($this, $model);
	}

	public function getConnectorQueries(\spitfire\storage\database\Query $parent) {
		$query = $this->getTarget()->getTable()->getAll();
		$query->setAliased(true);
		
		foreach ($this->getReferencedField()->getPhysical() as $p) {
			$query->addRestriction($p, $parent->queryFieldInstance($p->getReferencedField()));
		}
		
		return Array($query);
	}

}