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

	public function __construct ($database, $tablename = false) {
		$this->db = $database;

		if ($tablename)
			$this->tablename = environment::get('db_table_prefix') . $tablename;
	}
	
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
	 * Get's the table's primary key.
	 * 
	 * @return String Name of the primary key's column
	 */
	public function getPrimaryKey() {
		if ($this->primaryK) return $this->primaryK;
		else return $this->db->getPrimaryKey($this);
	}
	
	public function delete(databaseRecord $data) {
		$this->db->delete($this, $data);
	}

}