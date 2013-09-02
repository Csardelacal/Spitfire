<?php

namespace spitfire\model\adapters;

use \ManyToManyField;
use \Model;

class ManyToManyAdapter
{
	/**
	 * The field the parent uses to refer to this element.
	 *
	 * @var \spitfire\model\ManyToManyField
	 */
	private $field;
	private $parent;
	private $children;
	
	public function __construct(ManyToManyField$field, Model$model, $data = null) {
		$this->field  = $field;
		$this->parent = $model;
		
		if ($data !== null) $this->children = $data;
	}
	
	public function getQuery() {
		
		$table  = $this->field->getTarget()->getTable();
		$fields = $table->getModel()->getFields();
		$found  = null;
		
		foreach ($fields as $field) {
			if ($field instanceof ManyToManyField && $field->getTarget() === $this->field->getModel()) $found = $field;
		}
		
		return $table->getAll()->addRestriction($found->getName(), $this->parent->getQuery());
		
	}
	
	public function toArray() {
		if ($this->children) return $this->children;
		$this->children = $this->getQuery()->fetchAll();
		return $this->children;
	}
	
}