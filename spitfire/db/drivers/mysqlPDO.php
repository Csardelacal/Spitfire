<?php

namespace spitfire\storage\database\drivers;

use Model;
use spitfire\storage\database\DB;
use spitfire\storage\database\Table;
use spitfire\storage\database\Query;
use spitfire\storage\database\Field;
use spitfire\SpitFire;
use PDO;
use PDOException;
use privateException;
use databaseRecord;

class mysqlPDODriver extends stdSQLDriver implements Driver
{

	private $connection    = false;
	private $fields        = Array();
	
	/**@var mixed List of errors the repair() method can fix*/
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
			$stt = $con->query($statement);
			SpitFire::$debug->log("DB: " . $statement);
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
				SpitFire::$debug->log("DB: " . $statement);
				return $stt;
			}
			catch (Exception $e) {/*Ignore*/}
			
			throw new privateException("$msg (#$code) in query: $statement");
		}
		
		return $stt;
	}

	public function query(Table $table, Query $query, $fields = false) {

		#Get the SQL Statement
		$statement = parent::query($table, $query, $fields);
		$values    = Array();
		
		#Execute
		$stt = $this->execute($statement);
		
		return new mysqlPDOResultSet($table, $stt);
		
	}

	public function delete(Table $table, databaseRecord $data) {
		#Get the SQL Statement
		$statement = parent::delete($table, $data);
		#Prepare values
		$values  = Array();
		#Execute
		$this->execute($statement);
	}

	public function inc(Table $table, databaseRecord $data, $field, $value) {
		
		$statement = parent::inc($table, $data, $field, $value);
		
		$this->execute( $statement );
	}

	public function insert(Table $table, databaseRecord $data) {
		$statement = parent::insert($table, $data);
		$values    = $data->getData();
		
		$_values   = Array();
		foreach($values as $value) $_values[] = $value;
		
		$this->execute($statement);
		return $this->connection->lastInsertId();
	}

	public function update(Table $table, databaseRecord$data) {
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
	
	public function getTableInstance(DB$db, $tablename, Model$model) {
		return new MysqlPDOTable($db, $tablename, $model);
	}
	
	public function quote($text) {
		return $this->getConnection()->quote($text);
	}
}