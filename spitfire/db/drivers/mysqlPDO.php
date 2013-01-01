<?php

class _SF_mysqlPDODriver implements _SF_DBDriver
{

	private $connection = false;

	protected function connect() {

		$dsn  = 'mysql:dbname=' . environment::get('db_database') . ';host=' . environment::get('db_server');
		$user = environment::get('db_user');
		$pass = environment::get('db_pass');

		try {
			$this->connection = new PDO($dsn, $user, $pass);
			return true;
		} catch (Exception $e) {
			SpitFire::$debug->msg($e->getMessage());
			throw new privateException('DB Error');
			return false;
		}

	}

	public function getConnection() {
		if (!$this->connection) $this->connect();
		return $this->connection;
	}

	public function fetchFields(_SF_DBTable $table) {
		
		$statement = "DESCRIBE `{$table->getTablename()}` ";
		
		$con = $this->getConnection();
		$stt = $con->prepare($statement);
		$stt->execute();
		
		$error = $stt->errorInfo();
		if ($error[1]) throw new privateException($error[2], $error[1]);
		
		$fields = Array();
		while($row = $stt->fetch()) {
			$fields[] = $row['Field'];
		}
		
		return $fields;
	}
	
	public function escapeFieldName($name) {
		return "`$name`";
	}

	public function query(_SF_DBTable $table, _SF_DBQuery $query, $fields = false) {
		
		$offset = ($query->getPage() - 1) * $query->getResultsPerPage();
		$rpp    = $query->getResultsPerPage();
		
		if (!$fields) $fields = $table->getFields();
		
		$restrictions = implode(' AND ', $query->getRestrictions() );
		if (empty($restrictions)) $restrictions = '1';//If no restrictions are set fetch everything

		$statement = "SELECT " . 
				implode($fields, ', ') . 
				" FROM `{$table->getTablename()}` WHERE  " . 
				$restrictions;
				
		$order = $query->getOrder();
		if ( !empty($order) ) {
			$statement.= " ORDER BY ";
			$statement.= $this->order['field'] . ' ' . $this->order['mode'];
		}
		
		if ($rpp > 0) $statement.= " LIMIT $offset, $rpp";

		$con = $this->getConnection();
		$stt = $con->prepare($statement);
		
		$values = Array(); //Prepare the statement to be executed
		$restrictions = $query->getRestrictions();
		foreach($restrictions as $r) $values[$r->getRID()] = $r->getValue();
		$stt->execute( array_map(Array($table, 'convertOut'), $values) );
		
		$err = $stt->errorInfo();
		if ($err[1]) throw new privateException($err[2] . ' in query ' . $statement, $err[1]);
		
		return new _SF_mysqlPDOResultSet($stt);
		
	}

	public function set(_SF_DBTable $table, $data) {
		
		if (!$table->getFields()) throw new privateException('No database fields for table ' . $this->tablename);
		
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
		$stt->execute( array_map(Array($table, 'convertOut'), $data) );
		
		$err = $stt->errorInfo();
		if ($err[1]) throw new privateException($err[2] . ' in query ' . $statement, $err[1]);
		
		if ($stt->rowCount() == 1) return $con->lastInsertId();
		else return $data['id'];
		
	}
}