<?php

namespace spitfire\storage\database\drivers;

use spitfire\storage\database\Table;
use spitfire\storage\database\Query;
use spitfire\storage\database\Field;
use spitfire\SpitFire;
use spitfire\environment;
use PDO;
use PDOException;
use privateException;
use databaseRecord;

class mysqlPDODriver extends stdSQLDriver implements Driver
{

	private $connection    = false;
	private $fields        = Array();
	private $model;
	private $schema;
	
	#Caches
	private $tables = Array();
	
	private $errs = Array(
	    'HY093' => 'Wrong parameter count',
	    '42000' => 'Reserved word used as field name',
	    '23000' => 'Unique restraint violated.'
	);
	
	public function __construct($model, $options) {
		$this->model = $model;
		$this->schema = environment::get('db_database');
	}

	protected function connect() {

		$dsn  = 'mysql:dbname=' . environment::get('db_database') . ';host=' . environment::get('db_server');
		$user = environment::get('db_user');
		$pass = environment::get('db_pass');

		try {
			$this->connection = new PDO($dsn, $user, $pass);
			$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			return true;
		} catch (Exception $e) {
			SpitFire::$debug->log($e->getMessage());
			throw new privateException('DB Error. Connection refused by the server');
		}

	}

	public function getConnection() {
		if (!$this->connection) $this->connect();
		return $this->connection;
	}

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
	
	public function execute($statement) {
		
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
			throw new privateException("$msg (#$code) in query: $statement");
		}
		
		return $stt;
	}
	
	public function listTables() {
		$statement = "SELECT table_name FROM information_schema.tables " .
			"WHERE table_schema = '{$this->schema}'";
		$stt = $this->execute($statement);
		
		while ($row = $stt->fetch()) $this->tables[] = $row['table_name'];
	}

	public function exists(Table$table) {
		
		if (empty($this->tables)) $this->listTables();
		return (in_array($table->getTablename(), $this->tables));
		
	}
	
	public function createTable(Table$table) {
		$fields = $table->getFields();
		foreach ($fields as $name => &$f) {
			$f = new mysqlPDOField($f);
			$f = $name . ' ' . $f->columnDefinition();
		}
		
		$stt = "CREATE TABLE " . $table->getTablename();
		$stt.= "(";
		$stt.= implode(', ', $fields);
		$stt.= ")";
		
		return $this->execute($stt);
	}

	public function query(Table $table, Query $query, $fields = false) {

		#Get the SQL Statement
		$statement = parent::query($table, $query, $fields);
		$values    = Array();
		
		#Execute
		$stt = $this->execute($table, $statement, $values);
		
		return new mysqlPDOResultSet($table, $stt);
		
	}

	public function delete(Table $table, databaseRecord $data) {
		#Get the SQL Statement
		$statement = parent::delete($table, $data);
		#Prepare values
		$values  = Array();
		#Execute
		$this->execute($table, $statement, $values);
	}

	public function inc(Table $table, databaseRecord $data, $field, $value) {
		
		$statement = parent::inc($table, $data, $field);
		$values    = Array($value);
		
		$r         = $data->getUniqueRestrictions();
		foreach($r as $restriction) $values[] = $restriction->getValue ();
		
		$this->execute($table, $statement, $values);
	}

	public function insert(Table $table, databaseRecord $data) {
		$statement = parent::insert($table, $data);
		$values    = $data->getData();
		
		$_values   = Array();
		foreach($values as $value) $_values[] = $value;
		
		$this->execute($table, $statement, $_values);
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
		$this->execute($table, $statement, $_values);
		return $this->connection->lastInsertId();
		
	}
	
	public function quote($text) {
		return $this->connection->quote($text);
	}
}