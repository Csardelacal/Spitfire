<?php

class DBInterface extends _SF_MVC
{
	/**
	 *
	 * @var _SF_DBDriver 
	 */
	private $driver;
	
	public function __construct() {
		$driver = '_SF_' . environment::get('db_driver') . 'Driver';
		$this->driver = new $driver();
	}

	public function getConnection() {
		return $this->driver->getConnection();
	}

	/**
	 * Get the name of the table
	 * @param String $table
	 * @return _SF_DBTable
	 */
	public function __get($table) {
		
		//In case we request a model, view or controller
		if (parent::__get($table)) return parent::__get($table);
		
		$tableClass = $table.'Model';

		if (class_exists($tableClass)) return $this->{$table} = new $tableClass ($this);
		else return $this->{$table} = new _SF_DBTable($this, $table);
	}
	
	public function __call($name, $arguments) {
		return call_user_func_array(Array($this->driver, $name), $arguments);
	}

}