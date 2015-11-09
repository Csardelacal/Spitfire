<?php namespace spitfire\storage\database;

use \Model;
use spitfire\storage\database\Schema;
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

	/**
	 * A reference to the database driver loaded. This allows the system to 
	 * use several databases without the models colliding.
	 *
	 * @var DB
	 */
	protected $db;
	
	/**
	 * The model this table uses as template to create itself on the DBMS. This is
	 * one of the key components to Spitfire's ORM as it allows the DB engine to 
	 * create the tables automatically and to discover the data relations.
	 *
	 * @var Schema 
	 */
	protected $model;
	
	/**
	 * The prefixed name of the table. The prefix is defined by the environment
	 * and allows to have several environments on the same database.
	 *
	 * @var string
	 */
	protected $tablename;
	
	/**
	 * List of the physical fields this table handles. This array is just a 
	 * shortcut to avoid looping through model-fields everytime a query is
	 * performed.
	 *
	 * @var spitfire\storage\database\DBField[] 
	 */
	protected $fields;
	
	/**
	 * Contains the bean this table uses to generate forms for itself. The bean
	 * contains additional data to make the data request more user friendly.
	 * 
	 * @var CoffeBean
	 */
	protected $bean;
	
	protected $primaryK;
	protected $auto_increment;
	
	/**
	 * This variable holds a record cache for data accessed by id. This is useful
	 * due to the big amount of queries that simply request an item by it's id
	 * 
	 * @var \Model[]
	 */
	protected $cache = Array();


	protected $errors    = Array();

	/**
	 * Creates a new Database Table instance. The tablename will be used to find 
	 * the right model for the table and will be stored prefixed to this object.
	 * 
	 * @param DBInterface $database
	 * @param string|Schema $tablename
	 */
	public function __construct (DB$db, $tablename) {
		$this->db = $db;
		
		if ($tablename instanceof Schema) {
			$this->model = $tablename;
			$this->model->setTable($this);
		} else {
			$model = $tablename . 'Model';
			$this->model = new Schema($tablename, $this);

			if (class_exists($model)) {
				$m = new $model(null);
				$m->definitions($this->model);
			}
		}
		
		$this->tablename = environment::get('db_table_prefix') . $this->model->getTableName();
		
		$this->makeFields();
	}
	
	public function makeFields() {
		
		$fields   = $this->model->getFields();
		$dbfields = Array();
		
		foreach ($fields as $field) {
			$physical = $field->getPhysical();
			while ($phys = array_shift($physical)) { $dbfields[$phys->getName()] = $phys; }
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
			if ($name->getTable() === $this) { return $name; }
			else { throw new \privateException('Field ' . $name . ' does not belong to ' . $this); }
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
	 * @return string 
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
			if ($field->getLogicalField()->isPrimary()) $pk[$name] = $field;
		}
		
		return $this->primaryK = (array) $pk;
	}
	
	public function getAutoIncrement() {
		if ($this->auto_increment) return $this->auto_increment;
		
		//Implicit else
		$fields  = $this->getFields();
		$ai      = null;
		
		foreach($fields as $field) {
			if ($field->getLogicalField()->isAutoIncrement()) $ai = $field;
		}
		
		return  $this->auto_increment = $ai;
	}
	
	/**
	 * Looks for a record based on it's primary data. This can be one of the
	 * following:
	 * <ul>
	 * <li>A single basic data field like a string or a int</li>
	 * <li>A string separated by : to separate those fields (SF POST standard)</li>
	 * <li>An array with the data</li>
	 * </ul>
	 * 
	 * @param mixed $id
	 */
	public function getById($id) {
		#If the data is a string separate by colons
		if (!is_array($id)) $id = explode(':', $id);
		
		#Create a query
		$primary = $this->getPrimaryKey();
		$query   = $this->getQueryInstance();
		
		#Check if it's cachable and get the name of the id field
		//$cachable = count($primary) == 1;
		//$cachekey = ($cachable)? reset($id) : null;
		
		#Detect if there is a cache hit
		//if (isset($this->cache[$cachekey])) return $this->cache[$cachekey];
		
		#Add the restrictions
		while(count($primary))
			$query->addRestriction (array_shift($primary), array_shift($id));
		
		#Return the result
		$_return = $query->fetch();
		//if ($cachable) $this->cache[$cachekey] = $_return;
		
		return $_return;
	}
	
	public function cache(Model$model) {
		$pk = $model->getPrimaryData();
		if (count($pk) === 1) {
			$this->cache[reset($pk)] = $model; 
		}
	}
	
	public function hitCache($id) {
		if (isset($this->cache[$id])) return $this->cache[$id];
		else return null;
	}
	
	/**
	 * 
	 * @return \Schema
	 */
	public function getModel() {
		return $this->model;
	}
	
	/**
	 * Returns the bean this model uses to generate Forms to feed itself with data
	 * the returned value normally is a class that inherits from CoffeeBean.
	 * 
	 * @return \CoffeeBean
	 */
	public function getBean($name = null) {
		
		if (!$name) { $beanName = $this->model->getName() . 'Bean'; }
		else        { $beanName = $name . 'Bean'; }
		
		$bean = new $beanName($this);
		
		return $bean;
	}
	
	abstract public function create();
	abstract public function repair();
	
	/**
	 * Creates a new record in this table
	 * 
	 * @return Model Record for the selected table
	 */
	public function newRecord($data = Array()) {
		$classname = $this->getModel()->getName() . 'Model';
		
		if (class_exists($classname)) {
			return new $classname($this, $data);
		}
		else {
			return new Model($this, $data);
		}
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
	 * @param Model $data Data to be validated
	 * @return boolean 
	 */
	public function validate(Model$data) {
		
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
	abstract public function getFieldInstance(Field$field, $name, DBField$references = null);
	
	/**
	 * Increments a value on high read/write environments. Using update can
	 * cause data to be corrupted. Increment requires the data to be in sync
	 * aka. stored to database.
	 * 
	 * @param String $key
	 * @param int|float $diff
	 * @throws privateException
	 */
	public abstract function increment(Model$record, $key, $diff = 1);
	
	public abstract function delete(Model$record);
	public abstract function insert(Model$record);
	public abstract function update(Model$record);
	public abstract function restrictionInstance($query, DBField$field, $value, $operator = null);
	public abstract function queryInstance($table);
	public abstract function destroy();

}
