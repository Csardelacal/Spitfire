<?php

namespace spitfire\model\adapters;

use \ManyToManyField;
use \Model;
use \Iterator;
use \ArrayAccess;

class ManyToManyAdapter implements ArrayAccess, Iterator
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
	
	public function getBridgeRecordsQuery() {
		if ($this->field->getModel() === $this->field->getTarget()) {
			$bridge_fields = $this->field->getBridge()->getFields();
			$query = $this->field->getBridge()->getTable()->getAll();
			$group = $query->group();
			foreach($bridge_fields as $f) {
				if ($f->getTarget() === $this->field->getModel()) {
					$group->addRestriction($f->getName(), $this->parent);
				}
			}
			$bridge_records = $query;
		}
		else {
			$bridge_records = $this->field->getBridge()->getTable()->get($this->field->getModel()->getName(), $this->parent);
		}
		return $bridge_records;
	}
	
	public function store() {
		$bridge_records = $this->getBridgeRecordsQuery()->fetchAll();

		foreach($bridge_records as $r) $r->delete();

		//@todo: Change for definitive.
		$value = $this->toArray();
		foreach($value as $child) {
			$insert = new BridgeAdapter($this->field, $this->parent, $child);
			return $insert->makeRecord()->store();
		}
	}
	
	/**
	 * Returns the field that contains this data. This allows to query the table or 
	 * target with ease.
	 * 
	 * @return \ManyToManyField
	 */
	public function getField() {
		return $this->field;
	}
	
	public function toArray() {
		if ($this->children) return $this->children;
		$this->children = $this->getQuery()->fetchAll();
		return $this->children;
	}

	public function current() {
		if (!$this->children) $this->toArray();
		return current($this->children);
	}

	public function key() {
		if (!$this->children) $this->toArray();
		return key($this->children);
	}

	public function next() {
		if (!$this->children) $this->toArray();
		return next($this->children);
	}

	public function rewind() {
		if (!$this->children) $this->toArray();
		return reset($this->children);
	}

	public function valid() {
		if (!$this->children) $this->toArray();
		return !!current($this->children);
	}

	public function offsetExists($offset) {
		if (!$this->children) $this->toArray();
		return isset($this->children[$offset]);
		
	}

	public function offsetGet($offset) {
		if (!$this->children) $this->toArray();
		return $this->children[$offset];
	}

	public function offsetSet($offset, $value) {
		if (!$this->children) $this->toArray();
		$this->children[$offset] = $value;
	}

	public function offsetUnset($offset) {
		if (!$this->children) $this->toArray();
		unset($this->children[$offset]);
	}
	
}