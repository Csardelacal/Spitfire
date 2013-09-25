<?php

namespace spitfire\model\adapters;

use ChildrenField;
use Model;
use Iterator;
use ArrayAccess;

class ChildrenAdapter implements ArrayAccess, Iterator
{
	/**
	 * The field the parent uses to refer to this element.
	 *
	 * @var \spitfire\model\ManyToManyField
	 */
	private $field;
	private $parent;
	private $children;
	
	public function __construct(ChildrenField$field, Model$model, $data = null) {
		$this->field  = $field;
		$this->parent = $model;
		
		if ($data !== null) {
			$this->children = $data;
		}
	}
	
	public function getQuery() {
		
		$table  = $this->field->getTarget()->getTable();
				
		return $table->getAll()->addRestriction($this->field->getRole(), $this->parent->getQuery());
		
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