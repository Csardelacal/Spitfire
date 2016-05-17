<?php namespace spitfire\storage\database;

use BadMethodCallException;
use spitfire\cache\MemoryCache;
use spitfire\core\Environment;
use spitfire\exceptions\PrivateException;
use spitfire\io\CharsetEncoder;
use spitfire\mvc\MVC;
use Strings;

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
	
	private $tableCache;
	private $encoder;
	
	/**
	 * Creates an instance of DBInterface. If options are set it will import
	 * them. Otherwise it will try to read them from the current environment.
	 * 
	 * @param String $options Name of the database driver to be used. You can
	 *                       choose one of the DBInterface::DRIVER_? consts.
	 */
	public function __construct($options = null) {
		$this->server   = (isset($options['server']))?   $options['server']   : Environment::get('db_server');
		$this->user     = (isset($options['user']))?     $options['user']     : Environment::get('db_user');
		$this->password = (isset($options['password']))? $options['password'] : Environment::get('db_pass');
		$this->schema   = (isset($options['schema']))?   $options['schema']   : Environment::get('db_database');
		$this->prefix   = (isset($options['prefix']))?   $options['prefix']   : Environment::get('db_table_prefix');
		
		$this->tableCache = new MemoryCache();
		$this->encoder    = new CharsetEncoder(Environment::get('system_encoding'), _def($options['encoding'], Environment::get('database_encoding')));
	}
	
	/**
	 * The encoder will allow the application to encode / decode database that 
	 * is directed towards or comes from the database.
	 * 
	 * @return CharsetEncoder
	 */
	public function getEncoder() {
		return $this->encoder;
	}
	
	/**
	 * Converts data from the encoding the database has TO the encoding the
	 * system uses.
	 * 
	 * @deprecated since version 0.1-dev 20160514
	 * @param String $str The string encoded with the database's encoding
	 * @return String The string encoded with Spitfire's encoding
	 */
	public function convertIn($str) {
		trigger_error('Using deprecated function DB::convertIn()', E_USER_DEPRECATED);
		return $this->encoder->encode($str);
	}
	
	
	/**
	 * Converts data from the encoding the system has TO the encoding the
	 * database uses.
	 * 
	 * @deprecated since version 0.1-dev 20160514
	 * @param String $str The string encoded with Spitfire's encoding
	 * @return String The string encoded with the database's encoding
	 */
	public function convertOut($str) {
		trigger_error('Using deprecated function DB::convertOut()', E_USER_DEPRECATED);
		return $this->encoder->decode($str);
	}
	
	/**
	 * Attempts to repair schema inconsistencies. These method is not meant 
	 * to be called by the user but aims to provide an endpoint the driver 
	 * can use when running into trouble.
	 * 
	 * This method does not actually repair broken databases but broken schemas,
	 * if your database is broken or data on it corrupt you need to use the 
	 * DBMS specific tools to repair it.
	 * 
	 * Repairs the list of tables/models <b>currently</b> loaded into the db.
	 * If a model hasn't been accessed during execution it won't be listed
	 * here.
	 * 
	 * Please note, that this function is used only for maintenance and repair
	 * works on tables. Meaning that it is not relevant if <b>all</b> tables
	 * were imported.
	 */
	public function repair() {
		$tables = $this->tableCache->getAll();
		foreach ($tables as $table) {
			$table->repair();
		}
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
	 * @throws PrivateException If the table could not be found
	 * @return Table The database table adapter
	 */
	public function table($tablename) {
		
		#If the parameter is a Model, we get it's name
		if ($tablename instanceof Schema) {
			if (!class_exists($tablename->getName().'Model')) { return $this->tableCache->set($tablename->getName(), $this->getTableInstance($this, $tablename)); } 
			else                                              { $tablename = $tablename->getName(); }
		}
		
		#We just tested if it's a Schema, let's see if it's a string
		if (!is_string($tablename)) { throw new BadMethodCallException('DB::table requires Schema or string as argument'); }
		
		#If the table has already been imported continue
		if ($this->tableCache->contains($tablename)) { return $this->tableCache->get($tablename); }
		
		#Check if the literall table name can be found in the database
		try { return $this->tableCache->set($tablename, $this->makeTable($tablename)); }
		catch (PrivateException$e) { /* Silent failure. The table may not exist */}
		
		#It's common to refer to the table as a plural (i.e. users), let's check if it's that
		try { return $this->tableCache->set(Strings::singular($tablename), $this->makeTable(Strings::singular($tablename))); }
		catch (PrivateException$e) { /*Silently fail. The singular of this table may not exist either*/}
		
		#Get the OTF model
		try {	return $this->tableCache->set($tablename, $this->getTableInstance($this, $this->getOTFModel($tablename))); }
		catch (PrivateException$e) { /*Silent failure again*/}
		
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
