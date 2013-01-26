<?php

/**
 * This class simulates a table belonging to a database. This way we can query
 * and handle tables with 'compiler-friendly' code that will inform about errors
 * 
 * @package Spitfire.storage.database
 * @author César de la Cal <cesar@magic3w.com>
 */
class _SF_DBTable extends _SF_Queriable
{

	/** @var DBInterface  */
	protected $db        = false;
	protected $tablename = false;
	protected $primaryK  = false;
	protected $fields    = false;
	
	protected $errors    = Array();
	protected $rpp       = 20;

	/**
	 * Creates a new Database Table instance.
	 * 
	 * @param DBInterface $database
	 * @param String $tablename
	 */
	public function __construct (DBInterface $database, $tablename = false) {
		$this->db = $database;

		if ($tablename) {
			$prefix = environment::get('db_table_prefix');
			$this->tablename =  $prefix . $tablename;
		}
		
		#If the user has set a list of fields turn them into fields
		if ($this->fields) {
			foreach ($this->fields as &$field) {
				$field = new _SF_DBField($this, $field);
			}
		}
		
		#If the user has set primaries, turn them into fields.
		if ($this->primaryK) {
			$key = (array)  $this->primaryK;
			foreach($key as $_key) {
				$_key = new _SF_DBField($this, $_key, true);
			}
		}
	}
	
	/**
	 * Fetch the fields of the table the database works with. If the programmer
	 * has defined a custom set of fields to work with, this function will
	 * return the overriden fields.
	 * 
	 * @return mixed The fields this table handles.
	 */
	public function getFields() {
		if ($this->fields) $fields = $this->fields;
		else               $fields = $this->db->fetchFields($this);
		
		return $fields;
	}
	
	public function getField($name) {
		$fields = $this->getFields();
		foreach ($fields as $field) {
			if ($field->getName() == $name) return $field;
		}
	}
	
	/**
	 * Returns the name of the table that is being used. The table name
	 * includes the database's prefix.
	 *
	 * @return String 
	 */
	public function getTablename() {
		return $this->tablename;
	}
	
	/**
	 * Returns the database the table belongs to.
	 * @return _SF_DBInterface
	 */
	public function getDb() {
		return $this->db;
	}
	
	/**
	 * Get's the table's primary key. This will always return an array
	 * containing the fields the Primary Key contains.
	 * 
	 * @return Array Name of the primary key's column
	 */
	public function getPrimaryKey() {
		if ($this->primaryK) $pk = $this->primaryK;
		else $pk = $this->primaryK = $this->db->getPrimaryKey($this);
		
		return (array)$pk;
	}
	
	public function update(databaseRecord $data) {
		if (!$this->validate($data)) throw new privateException('Invalid data');
		return $this->db->update($this, $data);
	}
	
	public function insert(databaseRecord $data) {
		if (!$this->validate($data)) throw new privateException('Invalid data');
		return $this->db->insert($this, $data);
	}
	
	public function delete(databaseRecord $data) {
		$this->db->delete($this, $data);
	}
	
	/**
	 * If the table cannot handle the request it will pass it on to the db
	 * and add itself to the arguments list.
	 * 
	 * @param string $name
	 * @param mixed $arguments
	 */
	public function __call($name, $arguments) {
		#Add the table to the arguments for the db
		array_unshift($arguments, $this);
		#Pass on
		return call_user_func_array(Array($this->db, $name), $arguments);
	}
	
	
	########################################################################
	#VALIDATION
	########################################################################
	
	/**
	 * Validates a record passed as parameter to make sure the data it
	 * contains is valid and can be stored to the database. This function
	 * is automatically called by insert and update.
	 *
	 * @param databaseRecord $data Data to be validated
	 * @return boolean 
	 */
	public function validate(databaseRecord$data) {
		
		$this->errors = Array();
		$ok = true;
		
		foreach ($this->getFields(true) as $field) {
			
			$function = Array($this, 'validate' . ucfirst($field) );
			$value    = Array(&$data->$field);
			
			if (method_exists( $function[0], $function[1] ) ) {
				$ok = $ok && call_user_func_array($function, $value);
			}
		}
		
		return $ok;
	}
	
	/**
	 * Adds an error message to the list of errors, this is used by the 
	 * validation functions to send errors to the list returned by 
	 * Table::getErrors()
	 *
	 * @param string $msg Error message
	 */
	public function errorMsg($msg) {
		$this->errors[] = $msg;
	}
	
	public function getErrors() {
		if (empty ($this->errors)) return false;
		return $this->errors;
	}

}