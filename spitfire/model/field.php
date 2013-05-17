<?php

namespace spitfire\model;

/**
 * Represents a table's field in a database. Contains information about the
 * table the field belongs to, the name of the field and if it is (or not) a
 * primary key or auto-increment field.
 * 
 * @author César de la Cal <cesar@magic3w.com>
 */
class Field
{
	protected $name;
	protected $indexed;
	protected $unique;
	protected $primary;
	protected $auto_increment;
	protected $datatype;
	protected $references;
	protected $referencedField;
	
	const TYPE_INTEGER   = 'int';
	const TYPE_LONG      = 'long';
	const TYPE_STRING    = 'string';
	const TYPE_FILE      = 'file';
	const TYPE_TEXT      = 'txt';
	const TYPE_DATETIME  = 'datetime';
	
	/**
	 * Returns true if the field is an auto-increment field on the database.
	 * 
	 * @return boolean
	 */
	public function isAutoIncrement() {
		return $this->auto_increment;
	}
	
	public function setAutoIncrement($ai) {
		$this->auto_increment = $ai;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function setName($name) {
		$this->name = $name;
	}


	/**
	 * Returns true if the field belongs to the table's primary key. Keep in
	 * mind that a primary key can cover several fields.
	 * 
	 * @return boolean
	 */
	public function isPrimary() {
		return $this->primary;
	}
	
	public function setPrimary($primary) {
		$this->primary = $primary;
		return $this;
	}
	
	public function isUnique() {
		return $this->unique;
	}
	
	public function setUnique($unique) {
		$this->unique = $unique;
		return $this;
	}
	
	public function isIndexed() {
		return $this->indexed;
	}
	
	public function setIndexed($indexed) {
		$this->indexed = $indexed;
		return $this;
	}
	
	public function getReference() {
		return $this->references;
	}
	
	public function setReference(Reference$reference) {
		$this->references = $reference;
	}
	
	public function getReferencedField() {
		return $this->referencedField;
	}
	
	public function setReferencedField(Field$field) {
		$this->referencedField = $field;
	}
	
	public function getDataType() {
		return $this->datatype;
	}
	
	public function setDataType($type) {
		$this->datatype = $type;
	}
	
	public function getRawData() {
		return get_object_vars($this);
	}
	
	public function importRawData($field) {
		$data = $field->getRawData();
		foreach ($data as $f => $v) $this->$f = $v;
	}
	
	public function __toString() {
		return $this->name;
	}
	
}
