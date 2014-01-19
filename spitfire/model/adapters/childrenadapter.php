<?php

namespace spitfire\model\adapters;

use ChildrenField;
use Model;
use Iterator;
use ArrayAccess;

class ChildrenAdapter implements ArrayAccess, Iterator, AdapterInterface
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
		$this->children = $data;
	}
	
	public function getQuery() {
		
		$table  = $this->field->getTarget()->getTable();
				
		return $table->getAll()->addRestriction($this->field->getRole(), $this->parent->getQuery());
		
	}
	
	public function toArray() {
		if ($this->children !== null) return $this->children;
		$this->children = $this->getQuery()->fetchAll();
		return $this->children;
	}

	public function current() {
		if ($this->children === null) $this->toArray();
		return current($this->children);
	}

	public function key() {
		if ($this->children === null) $this->toArray();
		return key($this->children);
	}

	public function next() {
		if ($this->children === null) $this->toArray();
		return next($this->children);
	}

	public function rewind() {
		if ($this->children === null) $this->toArray();
		return reset($this->children);
	}

	public function valid() {
		if ($this->children === null) $this->toArray();
		return !!current($this->children);
	}

	public function offsetExists($offset) {
		if ($this->children === null) $this->toArray();
		return isset($this->children[$offset]);
		
	}

	public function offsetGet($offset) {
		if ($this->children === null) $this->toArray();
		return $this->children[$offset];
	}

	public function offsetSet($offset, $value) {
		if ($this->children === null) $this->toArray();
		$this->children[$offset] = $value;
	}

	public function offsetUnset($offset) {
		if ($this->children === null) $this->toArray();
		unset($this->children[$offset]);
	}

	public function commit() {
		
		$bridge_records = $this->getBridgeRecordsQuery()->fetchAll();

		foreach($bridge_records as $r) {
			$r->delete();
		}

		//@todo: Change for definitive.
		$value = $this->toArray();
		foreach($value as $child) {
			$insert = new BridgeAdapter($this->field, $this->parent, $child);
			$insert->makeRecord()->store();
		}
	}

	public function dbGetData() {
		return null;
	}
	
	/**
	 * This method does nothing as this field has no direct data in the DBMS and 
	 * therefore it just ignores whatever the database tries to input.
	 * 
	 * @param mixed $data
	 */
	public function dbSetData($data) {
		return;
	}
	
	/**
	 * Returns the parent model for this adapter. This allows any application to 
	 * trace what adapter this adapter belongs to.
	 * 
	 * @return \Model
	 */
	public function getModel() {
		return $this->parent;
	}
	
	public function isSynced() {
		return true;
	}

	public function rollback() {
		return true;
	}

	public function usrGetData() {
		return $this;
	}
	
	/**
	 * Defines the data inside this adapter. In case the user is trying to set 
	 * this adapter as the source for itself, which can happen in case the user
	 * is reading the adapter and expecting himself to save it back this function
	 * will do nothing.
	 * 
	 * @param \spitfire\model\adapters\ManyToManyAdapter|Model[] $data
	 * @throws \privateException
	 */
	public function usrSetData($data) {
		if ($data === $this) {
			return;
		}
		
		if ($data instanceof ManyToManyAdapter) {
			$this->children = $data->toArray();
		} elseif (is_array($data)) {
			$this->children = $data;
		} else {
			throw new \privateException('Invalid data. Requires adapter or array');
		}
	}

	public function getField() {
		return $this->field;
	}
	
	public function __toString() {
		return "Array()";
	}

}