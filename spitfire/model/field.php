<?php

namespace spitfire\model;

use Model;

/**
 * Represents a table's field in a database. Contains information about the
 * table the field belongs to, the name of the field and if it is (or not) a
 * primary key or auto-increment field.
 * 
 * @author César de la Cal <cesar@magic3w.com>
 */
class Field
{
	protected $indexed;
	protected $unique;
	protected $primary;
	protected $auto_increment;
	protected $datatype;
	protected $references;
	
	const TYPE_INTEGER = 'int';
	const TYPE_LONG    = 'long';
	const TYPE_STRING  = 'string';
	const TYPE_TEXT    = 'txt';
	
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
	}
	
	public function isUnique() {
		return $this->unique;
	}
	
	public function setUnique($unique) {
		$this->unique = $unique;
	}
	
	public function isIndexed() {
		return $this->indexed;
	}
	
	public function setIndexed($indexed) {
		$this->indexed = $indexed;
	}
	
	public function getReference() {
		return $this->references;
	}
	
	public function setReference(Model$model, $field) {
		$this->references = Array($model, $field);
	}
	
	public function getDataType() {
		return $this->datatype;
	}
	
	public function setDataType($type) {
		$this->datatype = $type;
	}
	
}
