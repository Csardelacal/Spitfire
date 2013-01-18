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

	/** @var _SF_DBInterface  */
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
	}
	
	/**
	 * Fetch the fields of the table the database works with. If the programmer
	 * has defined a custom set of fields to work with, this function will
	 * return the overriden fields.
	 * 
	 * @return mixed The fields this table handles.
	 */
	public function getFields() {
		if ($this->fields) return $this->fields;
		return $this->db->fetchFields($this);
	}
	
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
	
	public function getErrors() {
		if (empty ($this->errors)) return false;
		return $this->errors;
	}

	public function set ($data) {
		return $this->db->set($this, $data);
	}
	
	public function validate($data) {
		
		$this->errors = Array();
		$newdata = Array();
		foreach ($this->getFields() as $field) {
			if (method_exists ($this, 'validate_' . $field)) {
				$function = Array($this, 'validate_' . $field);
				$error = call_user_func_array($function, Array($data[$field]));
				if ($error) {
					$this->errors[] = $error;
				}
			}
			$newdata[$field] = $data[$field];
		}
		
		return $newdata;
	}
	
	/**
	 * Get's the table's primary key. This will always return an array
	 * containing the fields the Primary Key contains.
	 * 
	 * @return Array Name of the primary key's column
	 */
	public function getPrimaryKey() {
		#If our PK has already been set get it
		if ($this->primaryK) {
			$pk = $this->primaryK;
		}
		#Fetch the primary key
		else {
			$pk = $this->db->getPrimaryKey($this);
		}
		if (is_array($pk)) return $pk;
		else               return Array($pk);
	}
	
	public function delete(databaseRecord $data) {
		$this->db->delete($this, $data);
	}

}