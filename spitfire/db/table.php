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
	public function getFields($unescaped = false) {
		if ($this->fields) $fields = $this->fields;
		else               $fields = $this->db->fetchFields($this);
		
		if ($unescaped) return $fields;
		else return $this->getDb()->escapeFieldNames($this, $fields);
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
	
	public function getErrors() {
		if (empty ($this->errors)) return false;
		return $this->errors;
	}

	public function set ($data) {
		return $this->db->set($this, $data);
	}
	
	public function validate($data) {
		
		$this->errors = Array();
		
		$ok = true;
		
		foreach ($this->getFields(true) as $field) {
			
			$function = Array($this, 'validate' . ucfirst($field) );
			
			if (method_exists( $function[0], $function[1] ) ) {
				$ok = $ok && call_user_func_array($function, Array($data->$field));
			}
		}
		
		return $ok;
	}
	
	public function errorMsg($msg) {
		$this->errors[] = $msg;
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

}