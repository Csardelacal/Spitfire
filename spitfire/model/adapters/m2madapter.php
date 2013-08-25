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
	
	public function __construct(ManyToManyField$field, Model$model) {
		$this->field  = $field;
		$this->parent = $model;
	}
	
	public function toArray() {
		$table = $this->field->getTarget()->getTable();
		
		$fields = $table->getModel()->getFields();
		$found  = null;
		
		foreach ($fields as $field) {
			if ($field instanceof ManyToManyField && $field->getTarget() === $this->field->getModel()) $found = $field;
		}
		
		$this->children = $table->getAll()->addRestriction($found->getName(), $this->parent->getQuery())
				  ->fetchAll();
		
		return $this->children;
	}
	
}