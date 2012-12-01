<?php

class DBInterface extends _SF_MVC
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

	public function __get($table) {
		
		//In case we request a model, view or controller
		if (parent::__get($table)) return parent::__get($table);
		
		$tableClass = $table.'Model';

		if (class_exists($tableClass)) return $this->{$table} = new $tableClass ($this);
		else return $this->{$table} = new _SF_DBTable($this, $table);
	}

}