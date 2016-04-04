<?php namespace spitfire;

use Serializable;
use spitfire\storage\database\Schema;
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
abstract class Model implements Serializable
{
	
	/**
	 * The actual data that the record contains. The record is basically a wrapper
	 * around the array that allows to validate data on the go and to alert the 
	 * programmer about inconsistent types.
	 * 
	 * @var spitfire\model\adapters\AdapterInterface[] 
	 */
	private $data;
	
	/**
	 * Keeps information about the table that owns the record this Model represents.
	 * This allows it to power functions like store that require knowledge about 
	 * the database keeping the information.
	 * 
	 * @var Table
	 */
	private $table;
	
	#Status vars
	private $new = false;
	
	/**
	 * Creates a new record.
	 * 
	 * @param Table $table DB Table this record belongs to. Easiest way
	 *                       to get this is by using $this->model->*tablename*
	 * 
	 * @param mixed $data  Attention! This parameter is intended to be 
	 *                       used by the system. To create a new record, leave
	 *                       empty and use setData.
	 */
	public function __construct(Table$table = null, $data = null) {
		
		$this->table   = $table;
		$this->new     = empty($data);
		
		$this->makeAdapters();
		$this->populateAdapters($data);
	}
	
	/**
	 * This method is used to generate the 'template' for the table that allows
	 * spitfire to automatically generate tables and allows it to check the types
	 * of data and fix tables.
	 * 
	 * @return \spitfire\db\Schema
	 * @abstract
	 */
	public abstract function definitions(Schema$schema);
	
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
	 * This method stores the data of this record to the database. In case
	 * of database error it throws an Exception and leaves the state of the
	 * record unchanged.
	 * 
	 * @throws privateException
	 */
	public function store() {
		#Check if onbeforesave is there and use it.
		if (method_exists($this, 'onbeforesave')) {
			$this->onbeforesave();
		}
		
		#Decide whether to insert or update depending on the Model
		if ($this->new) { $this->insert(); }
		else            { $this->update(); }
		
		$this->new = false;
		
		foreach($this->data as $value) {
			$value->commit();
		}
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
			$logical = $field->getLogicalField();
			$ret = array_merge($ret, $this->data[$logical->getName()]->dbGetData());
	    }
	    
	    return $ret;
	}
	
	/**
	 * Creates a list of restrictions that identify this record inside it's
	 * database table.
	 * 
	 * @todo Fix, still works the old way.
	 * @deprecated since version 0.1
	 * @return Restriction[]
	 */
	public function getUniqueRestrictions() {
		$primaries    = $this->table->getPrimaryKey();
		$query        = $this->table->getQueryInstance();
		$restrictions = Array();
		$values       = Array();
		
		foreach($primaries as $primary) {
			$values[] = $this->data[$primary->getLogicalField()->getName()]->dbGetData();
		}
		
		foreach ($values as $primary => $value) {	
			$r = $query->restrictionInstance($query->queryFieldInstance($this->getTable()->getField($primary)), $value, '=');
			$restrictions[] = $r;
		}
		
		return $restrictions;
	}
	
	public function getQuery() {
		$query     = $this->getTable()->queryInstance($this->getTable());
		$primaries = $this->table->getModel()->getPrimary();
		
		foreach ($primaries as $primary) {
			$name = $primary->getName();
			$query->addRestriction($name, $this->$name);
		}
		
		return $query;
	}
	
	/**
	 * Returns the table this record belongs to.
	 * 
	 * @return spitfire\storage\database\Table
	 */
	public function getTable() {
		return $this->table;
	}

	public function __set($field, $value) {
		
		if (!isset($this->data[$field])) {
			throw new privateException("Setting non existent field: " . $field);
		}
		
		$this->data[$field]->usrSetData($value);
	}
	
	public function __get($field) {
		#If the field is in the record we return it's contents
		if (isset($this->data[$field])) {
			return $this->data[$field]->usrGetData();
		} else {
			//TODO: In case debug is enabled this should throw an exception
			return null;
		}
	}
	
	//TODO: This now breaks due to the adapters
	public function serialize() {
		$data = array();
		foreach($this->data as $adapter) {
			if (! $adapter->isSynced()) throw new privateException("Database record cannot be serialized out of sync");
			$data = array_merge($data, $adapter->dbGetData());
		}
		
		$output = Array();
		$output['model'] = $this->table->getModel()->getName();
		$output['data']  = $data;
		
		return serialize($output);
	}
	
	public function unserialize($serialized) {
		
		$input = unserialize($serialized);
		$this->table = db()->table($input['model']);
		
		$this->makeAdapters();
		$this->populateAdapters($input['data']);
	}
	
	public function __toString() {
		return sprintf('%s(%s)', $this->getTable()->getModel()->getName(), implode(',', $this->getPrimaryData()) );
	}
	
	public function delete() {
		$this->table->delete($this);
	}
	
	public function insert() {
		#Insert the record by calling the driver.
		$id = $this->table->insert($this);
		#Get the autoincrement field
		$ai = $this->table->getAutoIncrement();
		
		if ($ai) {
			$payload = array_filter($this->data[$ai->getName()]->dbGetData());
		}
		
		#If the autoincrement field is empty set the new DB given id
		if ($ai && empty($payload) ) {
			$this->data[$ai->getName()]->dbSetData(Array($ai->getName() => $id));
		}
		
		return $id;
	}
	
	public function update() {
		$this->table->update($this);
	}
	
	public function restrictionInstance($query, DBField$field, $value, $operator = null) {
		return $this->table->restrictionInstance($query, $field, $value, $operator);
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
	
	protected function makeAdapters() {
		#If there is no table defined there is no need to create adapters
		if ($this->table === null) { return; }
		
		$fields = $this->getTable()->getModel()->getFields();
		foreach ($fields as $field) {
			$this->data[$field->getName()] = $field->getAdapter($this);
		}
	}
	
	protected function populateAdapters($data) {
		#If the set carries no data, why bother reading?
		if (empty($data)) { return; }
		
		#Retrieves the full list of fields this adapter needs to populate
		$fields = $this->getTable()->getModel()->getFields();
		
		#Loops through the fields retrieving the physical fields
		foreach ($fields as $field) {
			$physical = $field->getPhysical();
			$current  = Array();
			
			#The physical fields are matched to the content and it is assigned.
			foreach ($physical as $p) {
				$current[$p->getName()] = $data[$p->getName()];
			}
			
			#Set the data into the adapter and let it work it's magic.
			$this->data[$field->getName()]->dbSetData($current);
		}
	}

}
