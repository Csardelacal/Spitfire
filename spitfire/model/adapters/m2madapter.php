<?php namespace spitfire\model\adapters;

use \ManyToManyField;
use \Model;
use \Iterator;
use \ArrayAccess;

/**
 * This adapter allows the user to access Many to many relations inside the 
 * database as if it was an array with sorted data. This makes it easy to iterate
 * over records or do actions on them. Please note that some of the actions 
 * performed on this records are very database intensive and are therefore to be
 * used with care.
 */
class ManyToManyAdapter implements ArrayAccess, Iterator, AdapterInterface
{
	/**
	 * The field the parent uses to refer to this element. This allows the app to 
	 * identify the kind of data it can receive and what table it is referring.
	 * @var \spitfire\model\ManyToManyField
	 */
	private $field;
	
	/**
	 * The 'parent' record. This helps locating the children field that should be
	 * fetched when accessing the content of the adapter.
	 * @var \Model 
	 */
	private $parent;
	
	/**
	 * The data stored into the adapter. This helps caching the data and keeping 
	 * a diff in case the data is modified.
	 * @var \Model[] 
	 */
	private $children;
	
	/**
	 * Creates a new adapter for fields that hold many to many data. This adapter
	 * allows you to access the related data for a 'parent' field on the related
	 * table.
	 * 
	 * You can use this adapter to develop applications simultating that the data 
	 * base can store arrays on relational stores.
	 * 
	 * @param ManyToManyField $field
	 * @param Model $model
	 * @param type $data - deprecated. Should no longer be used.
	 */
	public function __construct(ManyToManyField$field, Model$model, $data = null) {
		$this->field  = $field;
		$this->parent = $model;
		
		if ($data !== null) { 
			$this->children = $data;
		}
	}
	
	/**
	 * Gets the query that fetches the current set of data located inside the 
	 * database for this adapter. This allows the adapter to 'initialize' itself
	 * in case there was no data inside it when the user requested it.
	 * 
	 * @return \spitfire\storage\database\Query
	 */
	public function getQuery() {
		$table  = $this->field->getTarget()->getTable();
		$fields = $table->getModel()->getFields();
		$found  = null;
		
		#Locates the field that relates to the parent model
		foreach ($fields as $field) {
			if ($field instanceof ManyToManyField && $field->getTarget() === $this->field->getModel()) {
				$found = $field;
			}
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
	
	/**
	 * @deprecated since version 0.1-dev
	 */
	public function store() {
		$bridge_records = $this->getBridgeRecordsQuery()->fetchAll();

		foreach($bridge_records as $r) $r->delete();

		//@todo: Change for definitive.
		$value = $this->toArray();
		foreach($value as $child) {
			$insert = new BridgeAdapter($this->field, $this->parent, $child);
			$insert->makeRecord()->store();
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

	public function commit() {
		
		 $this->getBridgeRecordsQuery()->delete();

		//@todo: Change for definitive.
		$value = $this->toArray();
		foreach($value as $child) {
			$insert = new BridgeAdapter($this->field, $this->parent, $child);
			$insert->makeRecord()->store();
		}
	}

	public function dbGetData() {
		return Array();
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

}