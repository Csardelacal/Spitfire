<?php

class _SF_mysqlPDODriver extends _SF_stdSQLDriver implements _SF_DBDriver
{

	private $connection = false;
	private $fields     = Array();

	protected function connect() {

		$dsn  = 'mysql:dbname=' . environment::get('db_database') . ';host=' . environment::get('db_server');
		$user = environment::get('db_user');
		$pass = environment::get('db_pass');

		try {
			$this->connection = new PDO($dsn, $user, $pass);
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

	public function fetchFields(_SF_DBTable $table) {
		
		#If it's cached return the data
		if ($this->fields[$table->getTablename()]) 
			return $this->fields[$table->getTablename()];
		
		#Prepare the statement
		$statement = "DESCRIBE `{$table->getTablename()}` ";
		
		$con = $this->getConnection();
		$stt = $con->prepare($statement);
		$stt->execute();
		
		$error = $stt->errorInfo();
		if ($error[1]) throw new privateException($error[2], $error[1]);
		
		$fields = Array();
		while($row = $stt->fetch()) {
			$fields[] = $row['Field'];
			//TODO: Check for PK
		}
		
		return $this->fields[$table->getTablename()] = $fields;
	}
	
	public function escapeFieldName(&$name) {
		switch($name) {
			case 'unique':
			case 'groups':
			case 'group':
				$name = "`$name`";
		}
	}

	public function query(_SF_DBTable $table, _SF_DBQuery $query, $fields = false) {

		$statement = parent::query($table, $query, $fields);

		$con = $this->getConnection();
		$stt = $con->prepare($statement);
		
		$values = Array(); //Prepare the statement to be executed
		$_restrictions = $query->getRestrictions();
		foreach($_restrictions as $r) $values[] = $r->getValue();
		$stt->execute( array_map(Array($table->getDB(), 'convertOut'), $values) );
		
		$err = $stt->errorInfo();
		if ($err[1]) throw new privateException($err[2] . ' in query ' . $statement, $err[1]);
		
		return new _SF_mysqlPDOResultSet($table, $stt);
		
	}

	public function set(_SF_DBTable $table, $data) {
		
		$fields = $table->getFields();
		if (empty($fields)) throw new privateException('No database fields for table ' . $this->tablename);
		
		$data   = $table->validate($data);
		$errors = $table->getErrors();
		if (!empty($errors)) return false;

		if (empty($data['id'])) unset ($data['id']);

		$fields = array_keys($data);
		$escapedFields = array_map(Array($this, 'escapeFieldName'), $fields);
		$famt   = count($fields);

		#Prepare query
		$statement = "INSERT INTO `{$table->getTablename()}` (".
				implode(', ', $escapedFields) .") VALUES (:" . 
				implode(', :', $fields) . ") ON DUPLICATE KEY UPDATE ";
		
		for ($i = 0; $i < $famt; $i++) {
			$statement.= $escapedFields[$i] . " = :" . $fields[$i];
			if ($i < $famt-1) $statement.= ',';
			$statement.= ' ';
		}

		#Run query
		$con = $this->getConnection();
		$stt = $con->prepare($statement);
		$stt->execute( array_map(Array($table->getDB(), 'convertOut'), $data) );
		
		$err = $stt->errorInfo();
		if ($err[1]) throw new privateException($err[2] . ' in query ' . $statement, $err[1]);
		
		if ($stt->rowCount() == 1) return $con->lastInsertId();
		else return $data['id'];
		
	}

	public function delete(_SF_DBTable $table, $id) {
		
	}

	public function inc(_SF_DBTable $table, $data, $id) {
		
	}

	public function insert(_SF_DBTable $table, $data) {
		
	}

	public function update(_SF_DBTable $table, $data, $id) {
		
	}
}