<?php

namespace spitfire\storage\database\drivers;

use spitfire\storage\database\Table;
use spitfire\storage\database\Query;
use spitfire\storage\database\Field;
use spitfire\environment;
use PDO;
use databaseRecord;

class mysqlPDODriver extends stdSQLDriver implements Driver
{

	private $connection    = false;
	private $fields        = Array();
	private $schema;
	
	private $errs = Array(
	    'HY093' => 'Wrong parameter count',
	    '42000' => 'Reserved word used as field name',
	    '23000' => 'Unique restraint violated.'
	);

	protected function connect() {
		
		$this->schema = environment::get('db_database');

		$dsn  = 'mysql:dbname=' . environment::get('db_database') . ';host=' . environment::get('db_server');
		$user = environment::get('db_user');
		$pass = environment::get('db_pass');

		try {
			$this->connection = new PDO($dsn, $user, $pass);
			$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			return true;
		} catch (Exception $e) {
			SpitFire::$debug->msg($e->getMessage());
			throw new privateException('DB Error. Connection refused by the server');
		}

	}

	public function getConnection() {
		if (!$this->connection) $this->connect();
		return $this->connection;
	}

	public function fetchFields(Table$table) {
		
		#If it's cached return the data
		if ($this->fields[$table->getTablename()]) 
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
	
	public function getPrimaryKey($table) {
		
		$fields    = $this->fetchFields($table);
		$primaries = Array();
		
		foreach ($fields as $field) {
			if ($field->isPrimary() ) $primaries[] = $field;
		}
		
		return $primaries;
	}
	
	public function getAutoIncrement($table) {
		
		$fields = $this->fetchFields($table);
		
		foreach ($fields as $field) {
			if ($field->isAutoIncrement() ) return $field;
		}
	}
	
	public function escapeFieldNames(Table$table, $names) {
		
		foreach ($names as &$name){
			$name = "`{$table->getTablename()}`.`$name`";
		}
		return $names;
	}
	
	public function execute(Table$table, $statement, $values) {
		
		#Connect to the database and prepare the statement
		$con = $this->getConnection();
		$stt = $con->prepare($statement);
		echo "Executing: " . $statement . '<br />';
		
		try {
			#Execute the query
			$stt->execute( array_map(Array($table->getDB(), 'convertOut'), $values) );
		}
		catch(PDOException $e) {
			#Recover from exception, make error readable. Re-throw
			$code = $e->getCode();
			$err  = $stt->errorInfo();
			$msg  = $err[2] or $this->errs[$code];
			throw new privateException("$msg (#$code) in query: $statement");
		}
		
		return $stt;
	}

	public function query(Table $table, Query $query, $fields = false) {

		#Get the SQL Statement
		$statement = parent::query($table, $query, $fields);
		$values    = Array();
		
		#Execute
		$stt = $this->execute($table, $statement, $values);
		
		return new _SF_mysqlPDOResultSet($table, $stt);
		
	}

	public function delete(Table $table, databaseRecord $data) {
		#Get the SQL Statement
		$primary = $table->getPrimaryKey();
		$statement = parent::delete($table, $data);
		#Prepare values
		$values  = Array();
		foreach($primary as $key) $values[] = $data->$key;
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

	public function update(Table $table, databaseRecord$data, $id) {
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