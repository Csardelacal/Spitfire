<?php

use spitfire\storage\database\Table;
use \spitfire\storage\database\DBField;
use \spitfire\storage\database\Query;

/**
 * This class allows to track changes on database data along the use of a program
 * and creates interactions with the database in a safe way.
 * 
 * @todo Make this class implement Iterator
 * @author César de la Cal <cesar@magic3w.com>
 */
abstract class databaseRecord implements Serializable
{
	
	private $src;
	private $data;
	private $table;
	
	#Status vars
	private $synced;
	private $deleted;
	
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
	public function __construct(Table $table, $srcData = Array() ) {
		
		//TODO: Move to separate function/method
		//Parse referenced data
		if (!empty($srcData)) {
			$referenced = $table->getModel()->getReferencedModels();
			foreach ($referenced as /** @var Model Remote model */$model) {
				$name = $model->getName();
				$primary = $model->getPrimary();

				if (!isset($srcData[$name]) || !$srcData[$name] instanceof databaseRecord) {
					$fields = $table->getModel()->getReferencedFields($model);
					$query  = $table->getDB()->table($model)->getAll();

					foreach($fields as $field) {
						list($model, $f) = $field->getReference();
						$query->addRestriction($f->getName(), $srcData[$field->getName()]);
					}

					$srcData[$name] = $query;

				}
			}

			foreach($srcData as $index => $content) {
				if (!$table->getModel()->getField($index)) unset($srcData[$index]);
			}
		}
		
		
		$this->src     = $srcData;
		$this->data    = $srcData;
		$this->table   = $table;
		
		$this->synced  = !empty($srcData);
		$this->deleted = false;
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
	 * Returns the data that has been modified since it was created / last 
	 * stored.
	 * 
	 * @return mixed
	 */
	public function getDiff() {
		$changed = Array();
		
		foreach($this->data as $key => $value) {
			if ($value != $this->src[$key]) $changed[$key] = $value;
		}
		
		return $changed;
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
		
		if( !$this->table->validate($this)) {
			throw new privateException(_t('invalid_data'));
		}
		
		if (empty($this->src)) {
			$id = $this->insert();
			$ai = $this->table->getAutoIncrement();
			
			if ($ai && empty($this->data[$ai->getName()]) ) {
				$this->data[$ai->getName()] = $id;
			}
		}
		else {
			$this->update($this);
		}
		
		$this->synced = true;
		$this->src    = $this->data;
	}
	
	public function getErrors() {
		return $this->table->getErrors();
	}


	/**
	 * Returns the fields that compound the primary key of this record.
	 * 
	 * @return DBField[]
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
		    $ret[$field->getName()] = $this->data[$field->getName()];
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
			$r = $this->restrictionInstance($primary, $this->src[$primary->getName()]);
			$restrictions[] = $r;
		}
		
		return $restrictions;
	}
	
	/**
	 * Returns a query to fetch children of this record included in the 
	 * selected table.
	 * 
	 * @param DBTable $table
	 * @return Query
	 */
	public function getChildren($table) {
		$query = $this->queryInstance($table);
		$query->setParent($this);
		return $query;
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
		
		if (!isset($this->data[$field]) || $value != $this->data[$field]) 
			$this->synced = false;
		
		if ($this->table->getModel()->getField($field)) {
			$this->data[$field] = $value;
		}
		else throw new privateException ('Setting non-existent database field: ' . $field);

	}
	
	public function __get($field) {
		if (isset($this->data[$field])) {
			if ($this->data[$field] instanceof Query) {
				return $this->data[$field]->fetch();
			}
			else return $this->data[$field];
		}
		return null;
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
	
	
	public abstract function delete();
	public abstract function insert();
	public abstract function update();
	public abstract function restrictionInstance(DBField$field, $value, $operator = null);
	public abstract function queryInstance($table);
	
	/**
	 * Increments a value on high read/write environments. Using update can
	 * cause data to be corrupted. Increment requires the data to be in sync
	 * aka. stored to database.
	 * 
	 * @param String $key
	 * @param int|float $diff
	 * @throws privateException
	 */
	public abstract function increment($key, $diff = 1);
}
