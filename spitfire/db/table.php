<?php

namespace spitfire\storage\database;

use \databaseRecord;
use Model;
use spitfire\model\Field;
use spitfire\environment;

/**
 * This class simulates a table belonging to a database. This way we can query
 * and handle tables with 'compiler-friendly' code that will inform about errors
 * 
 * @package Spitfire.storage.database
 * @author César de la Cal <cesar@magic3w.com>
 */
abstract class Table extends Queriable
{

	protected $db;
	protected $model;
	protected $tablename;
	protected $fields;


	protected $errors    = Array();

	/**
	 * Creates a new Database Table instance.
	 * 
	 * @param DBInterface $database
	 * @param String $tablename
	 */
	public function __construct (DB$db, $tablename, Model$model) {
		$this->db = $db;
		$this->tablename = environment::get('db_table_prefix') . $tablename;
		
		$this->model = $model;
		$fields = $this->model->getFields();
		foreach ($fields as $name => &$f) {
			$f = $this->getFieldInstance($this, $name, $f);
		}
		
		$this->fields = $fields;
	}
	
	/**
	 * Fetch the fields of the table the database works with. If the programmer
	 * has defined a custom set of fields to work with, this function will
	 * return the overriden fields.
	 * 
	 * @return mixed The fields this table handles.
	 */
	public function getFields() {
		return $this->fields;
	}
	
	public function getField($name) {
		if (isset($this->fields[$name])) return $this->fields[$name];
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
	 * @return DB
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
		if ($this->primaryK)  return $this->primaryK;
		
		//Implicit else
		$fields  = $this->getFields();
		$pk      = Array();
		
		foreach($fields as $field) {
			if ($field->isPrimary()) $pk[] = $field;
		}
		
		return $this->primaryK = (array) $pk;
	}
	
	public function getAutoIncrement() {
		if ($this->auto_increment) return $this->auto_increment;
		
		//Implicit else
		$fields  = $this->getFields();
		$ai      = null;
		
		foreach($fields as $field) {
			if ($field->isAutoIncrement()) $ai = $field;
		}
		
		return  $this->auto_increment = $ai;
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
		$fields = $this->getFields();
		
		foreach ($fields as $name => $field) {
			
			$function = Array($this->model, 'validate' . ucfirst($name) );
			$value    = Array($data->$name);
			
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
	
	/**
	 * Creates an instance of the Database field compatible with the current
	 * DBMS
	 * 
	 * @return DBField Field
	 */
	abstract public function getFieldInstance(Table$t, $fieldname, Field$data);

}