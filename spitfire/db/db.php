<?php

/**
 * This class creates a "bridge" beetwen the classes that use it and the actual
 * driver.
 * 
 * @package Spitfire.storage
 * @author César de la Cal <cesar@magic3w.com>
 */
class DBInterface extends _SF_MVC
{
	/**
	 * Reference to the actual DB driver.
	 * @var _SF_DBDriver 
	 */
	private $driver;
	
	/**
	 * 
	 * @param String $driver
	 */
	public function __construct($driver = null) {
		
		#If the driver is not selected we get the one we want from env.
		if (is_null($driver)) $driver = environment::get('db_driver');
		
		#Instantiate the driver
		$driver = '_SF_' . $driver . 'Driver';
		$this->driver = new $driver();
	}

	/**
	 * Returns the handle used by the system to connect to the database.
	 * Depending on the driver this can be any type of content. It should
	 * only be used by applications with special needs.
	 * 
	 * @return mixed Connector
	 */
	public function getConnection() {
		return $this->driver->getConnection();
	}
	
	/**
	 * Converts data from the encoding the database has TO the encoding the
	 * system uses.
	 * @param String $str
	 * @return String
	 */
	public function convertIn($str) {
		return iconv(environment::get('database_encoding'), environment::get('system_encoding'), $str);
	}
	
	
	/**
	 * Converts data from the encoding the system has TO the encoding the
	 * database uses.
	 * @param String $str
	 * @return Strng
	 */
	public function convertOut($str) {
		return iconv(environment::get('system_encoding'), environment::get('database_encoding'), $str);
	}

	/**
	 * Returns a database table to allow querying and data-manipulation.
	 * 
	 * @param String $table Name of the table
	 * @return _SF_DBTable
	 */
	public function __get($table) {
		
		#In case we request a model, view or controller
		if (parent::__get($table)) return parent::__get($table);
		
		$tableClass = $table.'Model';

		if (class_exists($tableClass)) return $this->{$table} = new $tableClass ($this);
		else return $this->{$table} = new _SF_DBTable($this, $table);
	}
	
	/**
	 * If a function the system doesn't know is required, the DB will send it
	 * to the driver to handle.
	 * 
	 * @param String $name 
	 * @param mixed $arguments
	 * @return mixed
	 */
	public function __call($name, $arguments) {
		return call_user_func_array(Array($this->driver, $name), $arguments);
	}

}