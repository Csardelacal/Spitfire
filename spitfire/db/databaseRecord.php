<?php

use spitfire\storage\database\Table;
use spitfire\storage\database\DBField;
use spitfire\storage\database\Query;

/**
 * This class allows to track changes on database data along the use of a program
 * and creates interactions with the database in a safe way.
 * 
 * @todo Make this class implement Iterator
 * @author César de la Cal <cesar@magic3w.com>
 */
class databaseRecord implements Serializable
{
	
	/**
	 * The actual data that the record contains. The record is basically a wrapper
	 * around the array that allows to validate data on the go and to alert the 
	 * programmer about inconsistent types.
	 * 
	 * @var mixed 
	 */
	private $data;
	private $src;
	private $table;
	
	#Status vars
	private $new = false;
	private $synced = true;
	private $deleted = false;
	
	/**
	 * Creates a new record.
	 * 
	 * @param _SF_DBTable $table DB Table this record belongs to. Easiest way
	 *                       to get this is by using $this->model->*tablename*
	 * 
	 * @param mixed $srcData Attention! This parameter is intended to be 
	 *                       used by the system. To create a new record, leave
	 *                       empty and use setData.
	 */
	public function __construct(Table $table, $data) {
		
		$this->table   = $table;
		$this->data    = $data;
		$this->src     = $data;
		$this->new     = empty($data);
	}
	
	/**
	 * Receives the data of an array and stores it into this record. This
	 * does not verify the data is correct nor does it check if the data
	 * fits into the table. You're assumed to pass the data correctly.
	 * 
	 * @param mixed $newdata
	 */
	public function setData($newdata) {
		$this->data = $newdata;
	}
	
	/**
	 * Returns the data this record currently contains as associative array.
	 * Remember that this data COULD be invalid when using setData to provide
	 * it.
	 * 
	 * @return mixed
	 */
	public function getData() {
		return $this->data;
	}
	
	/**
	 * Data contained by the database. Note that it is possible that this
	 * function does provide data not actually in the DB (the record can have
	 * been destroyed or modified, or this data altered)
	 * 
	 * @return mixed
	 */
	public function getSrcData() {
		return $this->src;
	}
	
	/**
	 * This function checks whether the data contained in this record is
	 * 'in sync' with the DB. Being in sync means that the data contained
	 * by this record is supposed to be the same as the physical record
	 * on the DBMS.
	 * 
	 * @return boolean True if the data is in sync with the DB
	 */
	public function isSynced() {
		return $this->synced && !$this->deleted;
	}
	
	/**
	 * This method stores the data of this record to the database. In case
	 * of database error it throws an Exception and leaves the state of the
	 * record unchanged.
	 * 
	 * @throws privateException
	 */
	public function store() {
		
		if (is_callable(Array($this, 'onbeforesave'))) {
			$this->onbeforesave();
		}
		
		if( !$this->table->validate($this)) {
			throw new privateException(_t('invalid_data'));
		}
		
		if ($this->new) {
			$id = $this->insert();
			$ai = $this->table->getAutoIncrement();
			
			if ($ai && empty($this->data[$ai->getName()]) ) {
				$this->data[$ai->getName()] = $id;
			}
		}
		else {
			$this->update($this);
		}
		
		$this->src    = $this->data;
		$this->synced = true;
		$this->new    = false;
		
		foreach($this->data as $value)
			if (is_array($value)) {
				foreach ($value as $record) $record->store();
			}
	}
	
	public function getErrors() {
		return $this->table->getErrors();
	}


	/**
	 * Returns the fields that compound the primary key of this record.
	 * 
	 * @return DBField[]|spitfire\storage\database\DBField[]
	 */
	public function getUniqueFields() {
		return $this->table->getPrimaryKey();
	}
        
	/**
	 * Returns the values of the fields included in this records primary
	 * fields
	 * 
	 * @todo Find better function name
	 * @return Array
	 */
	public function getPrimaryData() {
		$primaryFields = $this->getUniqueFields();
		$ret = Array();
	    
		foreach ($primaryFields as $field) {
			if (null != $ref = $field->getReferencedField()) {
				$logical = $ref->getLogicalField();
				$name    = $field->getReferencedField()->getName();
				$ret[$field->getName()] = $this->{$logical->getName()}->{$name};
			}
			else {
				$ret[$field->getName()] = $this->{$field->getName()};
			}
	    }
	    
	    return $ret;
	}
	
	/**
	 * Creates a list of restrictions that identify this record inside it's
	 * database table.
	 * 
	 * @return Restriction[]
	 */
	public function getUniqueRestrictions() {
		$primaries    = $this->table->getPrimaryKey();
		$restrictions = Array();
		
		foreach($primaries as $primary) {
			$ref = $primary->getReferencedField();
			if ($ref) $value =& $this->src[$ref->getTable()->getModel()->getName()];
			else $value = $this->src[$primary->getName()];
			
			if ($value instanceof Query) $value = $value->fetch();
			if ($value instanceof databaseRecord) {
				unset($value);
				$value = $this->src[$ref->getTable()->getModel()->getName()]->{$ref->getName()};
			}
			
			$r = $this->restrictionInstance($primary, $value);
			$restrictions[] = $r;
			
			unset($value);
		}
		
		return $restrictions;
	}
	
	/**
	 * Returns the table this record belongs to.
	 * 
	 * @return Table
	 */
	public function getTable() {
		return $this->table;
	}

	public function __set($field, $value) {
		
		$field_info = $this->table->getModel()->getField($field);
		
		if ($field_info instanceof Reference) {
			if (!$value instanceof databaseRecord) {
				throw new privateException('Not a record');
			}
			else {
				$this->data[$field] = $value;
			}
		}
		elseif (!is_null($field_info)) {
			$this->data[$field] = $value;
		}
		else {
			throw new privateException ('Setting non-existent database field: ' . $field);
		}

	}
	
	public function __get($field) {
		
		$field_info = $this->table->getModel()->getField($field);
		
		if ($field_info instanceof Reference) {
			if ($this->data[$field] instanceof Query) {
				return $this->data[$field] = $this->data[$field]->fetch();
			} else {
				return $this->data[$field];
			}
		}
		
		elseif ($field_info instanceof ChildrenField) {
			if ($this->data[$field] instanceof Query) {
				return $this->data[$field] = $this->data[$field]->fetchAll($this);
			} else {
				return $this->data[$field];
			}
		}
		
		else {
			if (isset($this->data[$field])) return $this->data[$field];
			else return null;
		}
	}
	
	public function serialize() {
		if (! $this->synced) throw new privateException("Database record cannot be serialized out of sync");
		
		$output = Array();
		$output['model'] = $this->table->getModel()->getName();
		$output['data']  = $this->data;
		
		return serialize($output);
	}
	
	public function unserialize($serialized) {
		
		$input = unserialize($serialized);
		$this->table = db()->table($input['model']);
		$this->src   = $input['data'];
		$this->data  = $input['data'];
		$this->synced= true;
	}
	
	public function __toString() {
		return sprintf('%s(%s)', $this->getTable()->getModel()->getName(), implode(',', $this->getPrimaryData()) );
	}
	
	public function delete() {
		$this->table->delete($this);
	}
	
	public function insert() {
		return $this->table->insert($this);
	}
	
	public function update() {
		$this->table->update($this);
	}
	
	public function restrictionInstance(DBField$field, $value, $operator = null) {
		return $this->table->restrictionInstance($field, $value, $operator);
	}
	
	/**
	 * Increments a value on high read/write environments. Using update can
	 * cause data to be corrupted. Increment requires the data to be in sync
	 * aka. stored to database.
	 * 
	 * @param String $key
	 * @param int|float $diff
	 * @throws privateException
	 */
	public function increment($key, $diff = 1) {
		$this->table->increment($this, $key, $diff);
	}
}
