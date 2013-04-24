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
	
	protected $primaryK;
	protected $auto_increment;


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
		$dbfields = Array();
		foreach ($fields as $f) {
			$dbfields[$f->getName()] = $this->getFieldInstance($this, $f);
		}
		
		$this->fields = $dbfields;
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
		#If the data we get is already a DBField check it belongs to this table
		if ($name instanceof DBField) {
			if ($name->getTable() == $this) return $name;
			else throw new \privateException('Field ' . $name . ' does not belong to ' . $this);
		}
		#Otherwise search for it in the fields list
		if (isset($this->fields[(string)$name])) return $this->fields[(string)$name];
		#Else the table couldn't be found
		throw new \privateException('Field ' . $name . ' does not exist in ' . $this);
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
	 * @return DB|spitfire\storage\database\DB
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
		
		foreach($fields as $name => $field) {
			if ($field->isPrimary()) $pk[$name] = $field;
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
	
	/**
	 * 
	 * @return Model
	 */
	public function getModel() {
		return $this->model;
	}
	
	abstract public function create();
	abstract public function repair();
	
	/**
	 * Creates a new record in this table
	 * 
	 * @return databaseRecord Empty record for the selected table
	 */
	abstract public function newRecord();
	
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
			$value    = Array($data->$name, $this);
			
			if (method_exists( $function[0], $function[1] ) ) {
				$ok = call_user_func_array($function, $value) && $ok;
			}
			unset($errors);
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
	abstract public function getFieldInstance(Table$t, Field$data);

}
