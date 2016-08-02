<?php namespace spitfire\storage\database\drivers;

use Schema;
use spitfire\storage\database\DB;
use spitfire\storage\database\Table;
use spitfire\storage\database\Query;
use spitfire\storage\database\Field;
use spitfire\SpitFire;
use spitfire\environment;
use PDO;
use PDOException;
use spitfire\exceptions\PrivateException;
use Model;

/**
 * MySQL driver via PDO. This driver does <b>not</b> make use of prepared 
 * statements, prepared statements become too difficult to handle for the driver
 * when using several JOINs or INs. For this reason the driver has moved from
 * them back to standard querying.
 */
class mysqlPDODriver extends stdSQLDriver implements Driver
{

	private $connection    = false;
	private $fields        = Array();
	
	/**@var mixed List of errors the repair() method can fix. This include:
	 *     <ul>
	 *     <li>1051 - Unknown table.</li>
	 *     <li>1054 - Unknown column</li>
	 *     <li>1146 - No such table</li>
	 *     </ul>
	 */
	private $reparable_errors = Array(1051, 1054, 1146);
	
	
	/**
	 * Establishes the connection with the database server. This function
	 * requires no parameters as they're stored by the class already.
	 * 
	 * @return boolean
	 * @throws PrivateException If the database was unable to establish a 
	 *                          connection because the Server rejected the connection.
	 */
	protected function connect() {
		
		$dsn  = 'mysql:dbname=' . $this->schema . ';host=' . $this->server . ';charset=' . $this->getEncoder()->getInnerEncoding();
		$user = $this->user;
		$pass = $this->password;

		try {
			$this->connection = new PDO($dsn, $user, $pass);
			$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->connection->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_NATURAL);
			
			return true;
		} catch (Exception $e) {
			SpitFire::$debug->log($e->getMessage());
			throw new PrivateException('DB Error. Connection refused by the server');
		}

	}

	/**
	 * Checks if a connection to the DB exists and creates it in case it
	 * does not exist already.
	 * 
	 * @return PDO
	 */
	public function getConnection() {
		if (!$this->connection) $this->connect();
		return $this->connection;
	}

	/**
	 * List the fields of a table
	 * 
	 * @deprecated since version 0.1Dev
	 * @param \spitfire\storage\database\Table $table
	 * @return mixed
	 */
	public function fetchFields(Table$table) {
		
		#If it's cached return the data
		if (isset($this->fields[$table->getTablename()])) 
			return $this->fields[$table->getTablename()];
		
		#Prepare the statement
		$statement = "DESCRIBE `{$table->getTablename()}` ";
		$stt = $this->execute($table, $statement, Array());
		
		$fields = Array();
		while($row = $stt->fetch()) {
			
			$primary        = strstr($row['Key'], 'PRI');
			$auto_increment = strstr($row['Extra'], 'auto_increment');
			$name           = $row['Field'];
			
			$fields[$name] = new Field($table, $name, $primary, $auto_increment);
			
		}
		
		return $this->fields[$table->getTablename()] = $fields;
	}
	
	/**
	 * Sends a query to the database server and returns the handle for the
	 * resultset the server / native driver returned.
	 * 
	 * @param string $statement SQL to be executed by the server.
	 * @param boolean $attemptrepair Defines whether the server should try
	 *                    to repair any model inconsistencies the server 
	 *                    encounters.
	 * @return PDOStatement
	 * @throws PrivateException In case the query fails for another reason
	 *                     than the ones the system manages to fix.
	 */
	public function execute($statement, $parameters = Array(), $attemptrepair = true) {
		#Connect to the database and prepare the statement
		$con = $this->getConnection();
		
		try {
			spitfire()->log("DB: " . $statement);
			#Execute the query
			$stt = $con->prepare($statement);
			$stt->execute();
			
			return $stt;
		
		} catch(PDOException $e) {
			#Recover from exception, make error readable. Re-throw
			$code = $e->getCode();
			$err  = $e->errorInfo;
			$msg  = $err[2]? $err[2] : 'Unknown error';
			
			#If the error is not repairable or the system is blocking repairs throw an exception
			if (!in_array($err[1], $this->reparable_errors) || !$attemptrepair) 
				{ throw new PrivateException("Error {$code} [{$msg}] captured. Not repairable", 1511081930, $e); }
			
			#Try to solve the error by checking integrity and repeat
			$this->repair();
			return $this->execute($statement, $parameters, false);
		}
	}

	/**
	 * 
	 * @deprecated since version 0.1Dev
	 * @param \spitfire\storage\database\Table $table
	 * @param \spitfire\storage\database\Query $query
	 * @param type $fields
	 * @return \spitfire\storage\database\drivers\mysqlPDOResultSet
	 */
	public function query(Table $table, Query $query, $fields = false) {

		#Get the SQL Statement
		$statement = parent::query($table, $query, $fields);
		$values    = Array();
		
		#Execute
		$stt = $this->execute($statement);
		
		return new mysqlPDOResultSet($table, $stt);
		
	}

	/**
	 * 
	 * @deprecated since version 0.1Dev
	 * @param \spitfire\storage\database\Table $table
	 * @param Model $data
	 */
	public function delete(Table $table, Model $data) {
		#Get the SQL Statement
		$statement = parent::delete($table, $data);
		#Prepare values
		$values  = Array();
		#Execute
		$this->execute($statement);
	}

	/**
	 * 
	 * @deprecated since version 0.1Dev
	 * @param \spitfire\storage\database\Table $table
	 * @param Model $data
	 * @param type $field
	 * @param type $value
	 */
	public function inc(Table $table, Model $data, $field, $value) {
		
		$statement = parent::inc($table, $data, $field, $value);
		
		$this->execute( $statement );
	}

	/**
	 * 
	 * @deprecated since version 0.1Dev
	 * @param \spitfire\storage\database\Table $table
	 * @param Model $data
	 * @return type
	 */
	public function insert(Table $table, Model $data) {
		$statement = parent::insert($table, $data);
		$values    = $data->getData();
		
		$_values   = Array();
		foreach($values as $value) $_values[] = $value;
		
		$this->execute($statement);
		return $this->connection->lastInsertId();
	}

	/**
	 * 
	 * @deprecated since version 0.1Dev
	 * @param \spitfire\storage\database\Table $table
	 * @param Model $data
	 * @return type
	 */
	public function update(Table $table, Model$data) {
		$statement = parent::update($table, $data);
		$values = $data->getDiff();
		
		#Convert the values to an array PDO can use
		$_values   = Array();
		foreach($values as $value) $_values[] = $value;
		
		#Add the restrictions
		$restrictions = $data->getUniqueRestrictions();
		foreach($restrictions as $r) $_values[] = $r->getValue();
		
		#Query
		$this->execute($statement);
		return $this->connection->lastInsertId();
		
	}
	
	/**
	 * Creates a new Table and returns it.
	 * 
	 * @deprecated since version 0.1-dev 1607020148
	 * @param spitfire.storage.database.DB $db
	 * @param string $tablename
	 * @param Schema $model
	 * @return spitfire.storage.database.drivers.MysqlPDOTable
	 */
	public function getTableInstance(DB$db, $tablename) {
		return new MysqlPDOTable($db, $tablename);
	}
	
	/**
	 * Escapes a string to be used in a SQL statement. PDO offers this
	 * functionality out of the box so there's nothing to do.
	 * 
	 * @param string $text
	 * @return string Quoted and escaped string
	 */
	public function quote($text) {
		if ($text === null)  return 'null';
		if ($text ===    0)  return "'0'";
		if ($text === false) return "'0'";
		
		$str = $this->getEncoder()->encode($text);
		return $this->getConnection()->quote( $str );
	}
	
	/**
	 * 
	 * @deprecated since version 0.1-dev 1607020148
	 * @param type $tablename
	 * @return \OTFModel
	 */
	public function getOTFModel($tablename) {
		$model = new \OTFModel();
		
		$fields = $this->execute("DESCRIBE `" . environment::get('db_table_prefix') . $tablename . "`", false);
		
		while ($row = $fields->fetch()) {
			$model->field($row['Field'], 'TextField');
		}
		
		$model->setName($tablename);
		return $model;
	}
	
	public function getObjectFactory() {
		static $factory;
		return $factory? : $factory = new \storage\database\drivers\mysqlpdo\ObjectFactory();
	}
}
