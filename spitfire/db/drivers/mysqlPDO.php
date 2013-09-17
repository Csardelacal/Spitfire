<?php

namespace spitfire\storage\database\drivers;

use Schema;
use spitfire\storage\database\DB;
use spitfire\storage\database\Table;
use spitfire\storage\database\Query;
use spitfire\storage\database\Field;
use spitfire\SpitFire;
use spitfire\environment;
use PDO;
use PDOException;
use privateException;
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
	 * @throws privateException If the database was unable to establish a 
	 *                          connection because the Server rejected the connection.
	 */
	protected function connect() {

		$dsn  = 'mysql:dbname=' . $this->schema . ';host=' . $this->server;
		$user = $this->user;
		$pass = $this->password;

		try {
			$this->connection = new PDO($dsn, $user, $pass);
			$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			return true;
		} catch (Exception $e) {
			SpitFire::$debug->log($e->getMessage());
			throw new privateException('DB Error. Connection refused by the server');
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
	 * @throws privateException In case the query fails for another reason
	 *                     than the ones the system manages to fix.
	 */
	public function execute($statement, $attemptrepair = true) {
		#Connect to the database and prepare the statement
		$con = $this->getConnection();
		
		try {
			spitfire()->log("DB: " . $statement);
			$stt = $con->query($statement);
			#Execute the query
		}
		catch(PDOException $e) {
			#Recover from exception, make error readable. Re-throw
			$code = $e->getCode();
			$err  = $this->connection->errorInfo();
			$msg  = $err[2] or $this->errs[$code];
			
			#Try to solve the error by checking integrity and repeat
			if (in_array($err[1], $this->reparable_errors)) 
			if ($attemptrepair)
			try{
				$this->repair();
				$stt = $con->query($statement);
				spitfire()->log("DB: " . $statement);
				return $stt;
			}
			catch (Exception $e) {/*Ignore*/}
			
			throw new privateException("$msg (#$code) in query: $statement");
		}
		
		return $stt;
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
		if ($text === null) return 'null';
		$str = $this->convertOut(strval($text));
		return $this->getConnection()->quote( $str );
	}

	public function getOTFModel($tablename) {
		$model = new \OTFModel();
		
		$fields = $this->execute("DESCRIBE `" . environment::get('db_table_prefix') . $tablename . "`", false);
		
		while ($row = $fields->fetch()) {
			$model->field($row['Field'], 'TextField');
		}
		
		$model->setName($tablename);
		return $model;
	}
}