<?php namespace spitfire\storage\database;

use spitfire\mvc\MVC;
use Strings;
use spitfire\exceptions\PrivateException;
use spitfire\environment;

/**
 * This class creates a "bridge" beetwen the classes that use it and the actual
 * driver.
 * 
 * @author César de la Cal <cesar@magic3w.com>
 */
abstract class DB 
{
	
	const MYSQL_PDO_DRIVER = 'mysqlPDO';
	
	protected $server;
	protected $user;
	protected $password;
	protected $schema;
	protected $prefix;
	
	protected $tables = Array();
	
	/**
	 * Creates an instance of DBInterface. If options are set it will import
	 * them. Otherwise it will try to read them from the current environment.
	 * 
	 * @param String $options Name of the database driver to be used. You can
	 *                       choose one of the DBInterface::DRIVER_? consts.
	 */
	public function __construct($options = null) {
		$this->server   = (isset($options['server']))?   $options['server']   : environment::get('db_server');
		$this->user     = (isset($options['user']))?     $options['user']     : environment::get('db_user');
		$this->password = (isset($options['password']))? $options['password'] : environment::get('db_pass');
		$this->schema   = (isset($options['schema']))?   $options['schema']   : environment::get('db_database');
		$this->prefix   = (isset($options['prefix']))?   $options['prefix']   : environment::get('db_table_prefix');
	}
	
	/**
	 * Converts data from the encoding the database has TO the encoding the
	 * system uses.
	 * @param String $str The string encoded with the database's encoding
	 * @return String The string encoded with Spitfire's encoding
	 */
	public function convertIn($str) {
		return iconv(environment::get('database_encoding'), environment::get('system_encoding').'//TRANSLIT', $str);
	}
	
	
	/**
	 * Converts data from the encoding the system has TO the encoding the
	 * database uses.
	 * @param String $str The string encoded with Spitfire's encoding
	 * @return String The string encoded with the database's encoding
	 */
	public function convertOut($str) {
		return iconv(environment::get('system_encoding'), environment::get('database_encoding').'//TRANSLIT', $str);
	}
	
	/**
	 * Attempts to repair schema inconsistencies. These method is not meant 
	 * to be called by the user but aims to provide an endpoint the driver 
	 * can use when running into trouble.
	 * 
	 * This method does not actually repair broken databases but broken schemas,
	 * if your database is broken or data on it corrupt you need to use the 
	 * DBMS specific tools to repair it.
	 */
	public function repair() {
		$tables = $this->getTables();
		foreach ($tables as $table) {
			$table->repair();
		}
	}
	
	/**
	 * Returns the list of tables/models <b>currently</b> loaded into the db.
	 * If a model hasn't been accessed during execution it won't be listed
	 * here.
	 * Please note, that this function is used only for maintenance and repair
	 * works on tables. Meaning that it is not relevant if <b>all</b> tables
	 * were imported.
	 * 
	 * @return mixed Array of tables imported.
	 */
	public function getTables() {
		return $this->tables;
	}
	
	/**
	 * Returns a table adapter for the database table with said name to allow
	 * querying and data-manipulation..
	 * 
	 * @param string|Schema $tablename Name of the table that should be used.
	 *                 If you pass a model to this function it will automatically
	 *                 read the name from the model and use it to find the 
	 *                 table.
	 * 
	 * @return Table The database table adapter
	 */
	public function table($tablename) {
		
		#If the table has already been imported continue
		if ($this->hasTable($tablename)) { return $this->getTableFromCache($tablename); }
		
		#If the parameter is a Model, we get it's name
		if ($tablename instanceof Schema) {
			if (!class_exists($tablename->getName().'Model')) { return $this->addTableToCache($tablename); } 
			else                                              { $tablename = $tablename->getName(); }
		}
		
		if (is_string($tablename)) {
			
			try { return $this->addTableToCache($this->makeTable($tablename)); }
			catch (\spitfire\exceptions\PrivateException$e) { /* Silent failure. The table may not exist */}
			
			try { return $this->addTableToCache($this->makeTable(Strings::singular($tablename))); }
			catch (\spitfire\exceptions\PrivateException$e) { /*Silently fail. The singular of this table may not exist either*/}
			
			/*
				$model = $this->getOTFModel($tablename);
				return $this->tables[$tablename] = $this->getTableInstance($this, $model->getTableName(), $model);
			}*/
			
		}
		
		#If all our ressources have come to an end... Halt it.
		throw new PrivateException('No table ' . $tablename . ' found');
		
	}
	
	/**
	 * 
	 * @todo Extract these methods (makeTable, hasTable, getTableFromCache) to a separate TablePool class
	 * @param string $tablename
	 * @return Table
	 * @throws PrivateException
	 */
	protected function makeTable($tablename) {
		$className = $tablename . 'Model';
		
		if (class_exists($className)) {
			#Create a schema and a model
			$schema = new Schema($tablename);
			$model = new $className();
			$model->definitions($schema);

			return $this->getTableInstance($this, $schema);
		}
		
		throw new PrivateException('No table ' . $tablename);
	}
	
	/**
	 * 
	 * @param string|Schema $name
	 * @return boolean
	 */
	protected function hasTable($name) {
		#If the variable we're passing is a schema we need to get it's name to look it up
		if ($name instanceof Schema) { $name = $name->getName(); }
		
		#Otherwise we default to looking up the array key
		return isset($this->tables[$name]);
	}
	
	/**
	 * 
	 * @param string|Schema $name
	 * @return Table
	 */
	protected function getTableFromCache($name) {
		#If the variable we're passing is a schema we need to get it's name to look it up
		if ($name instanceof Schema) { $name = $name->getName(); }
		
		#Otherwise we default to looking up the array key
		return $this->tables[$name];
	}
	
	/**
	 * 
	 * @param Table|Schema $table
	 * @return Table
	 */
	protected function addTableToCache($table) {
		#If the variable we're passing is a schema we need to get it's name to look it up
		if ($table instanceof Schema) { $table = $this->getTableInstance($this, $table); }
		
		#Otherwise we default to looking up the array key
		return $this->tables[$table->getModel()->getName()] = $table;
	}

	/**
	 * Allows short-hand access to tables by using: $db->tablename
	 * 
	 * @param String $table Name of the table
	 * @return Table|MVC
	 */
	public function __get($table) {
		#Otherwise we try to get the table with this name
		return $this->table($table);
	}

	/**
	 * Returns the handle used by the system to connect to the database.
	 * Depending on the driver this can be any type of content. It should
	 * only be used by applications with special needs.
	 * 
	 * @abstract
	 * @return mixed The connector used by the system to communicate with
	 * the database server. The data-type of the return value depends on
	 * the driver used by the system.
	 */
	abstract public function getConnection();
	
	/**
	 * Returns an instance of the class the child tables of this class have
	 * this is used to create them when requested by the table() method.
	 * 
	 * Note: This is deprecated in favor of a factory object that could generalize
	 * driver specific object creation.
	 * 
	 * @abstract
	 * @deprecated since version 0.1-dev 20160406
	 * @return Table Instance of the table class the driver wants the system to use
	 */
	abstract public function getTableInstance(DB$db, $tablename);
	
	/**
	 * Creates a new On The Fly Model. These allow the system to interact with a 
	 * database that was not modeled after Spitfire's models or that was not 
	 * reverse engineered previously.
	 * 
	 * Note: This is deprecated in favor of a factory object that could generalize
	 * driver specific object creation.
	 * 
	 * @abstract
	 * @deprecated since version 0.1-dev 20160406
	 * @return Table Instance of the table class the driver wants the system to use
	 */
	abstract public function getOTFModel($tablename);

}