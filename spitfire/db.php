<?php

class DBInterface
{

	private $connection = false;

	protected function connect() {

		$dsn  = 'mysql:dbname=' . environment::get('db_database') . ';host=' . environment::get('db_server');
		$user = environment::get('db_user');
		$pass = environment::get('db_password');

		try {
			$this->connection = new PDO($dsn, $user, $pass);
			return true;
		} catch (Exception $e) return false;

	}

	public function getConnection() {
		if (!$this->connection) $this->connect();
		return $this->connection;
	}

	public function __get($table) {
		if (class_exists($table.'Model')) return new {$table.'Model'}($this);
		else return new table($this, $table);
	}

}