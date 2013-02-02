<?php

/**
 * Namespace related to all the functions spitfire offers to handle different
 * relational engines.
 * 
 * @package spitfire.storage.database
 */
namespace spitfire\storage\database;

use _SF_MVC;
use Model;
use privateException;
use spitfire\environment;

/**
 * This class creates a "bridge" beetwen the classes that use it and the actual
 * driver.
 * 
 * @package Spitfire.storage.database
 * @author César de la Cal <cesar@magic3w.com>
 */
abstract class DB extends _SF_MVC
{
	
	const MYSQL_PDO_DRIVER = 'mysqlPDO';
	
	/**
	 * Creates an instance of DBInterface
	 * @param String $driver Name of the database driver to be used. You can
	 *                       choose one of the DBInterface::DRIVER_? consts.
	 */
	public function __construct($options = null) {
		//TODO: Create logic
		
	}
	
	/**
	 * Converts data from the encoding the database has TO the encoding the
	 * system uses.
	 * @param String $str The string encoded with the database's encoding
	 * @return String The string encoded with Spitfire's encoding
	 */
	public function convertIn($str) {
		return iconv(environment::get('database_encoding'), environment::get('system_encoding'), $str);
	}
	
	
	/**
	 * Converts data from the encoding the system has TO the encoding the
	 * database uses.
	 * @param String $str The string encoded with Spitfire's encoding
	 * @return String The string encoded with the database's encoding
	 */
	public function convertOut($str) {
		return iconv(environment::get('system_encoding'), environment::get('database_encoding'), $str);
	}
	
	public function repair() {
		$tables = $this->getTables();
		foreach ($tables as $table) {
			$table->repair();
		}
	}
	
	public function getTables() {
		$tables = get_object_vars($this);
		foreach ($tables as $name => $value) {
			if (! $value instanceof Table) unset($tables[$name]);
		}
		return $tables;
	}
	
	/**
	 * Returns a table adapter for the database table with said name to allow
	 * querying and data-manipulation..
	 * 
	 * @param String $tablename Name of the table that should be used.
	 * @return Table The database table adapter
	 */
	public function table($tablename) {
		if (isset($this->{$tablename})) return $this->{$tablename};
		
		$modelName = $tablename.'Model';

		if (class_exists($modelName)) {
			$model = new $modelName;
			return $this->{$tablename} = $this->getTableInstance($this, $tablename, $model);
		}
		else throw new privateException('Unknown model ' . $modelName);
	}

	/**
	 * Allows short-hand access to tables by using: $db->tablename
	 * 
	 * @param String $table Name of the table
	 * @return Table|_SF_MVC
	 */
	public function __get($table) {
		#In case we request a model, view or controller
		if (parent::__get($table)) return parent::__get($table);
		#Otherwise we try to get the table with this name
		return $this->table($table);
	}

	/**
	 * Returns the handle used by the system to connect to the database.
	 * Depending on the driver this can be any type of content. It should
	 * only be used by applications with special needs.
	 * 
	 * @return mixed The connector used by the system to communicate with
	 * the database server. The data-type of the return value depends on
	 * the driver used by the system.
	 */
	abstract public function getConnection();
	
	/**
	 * Returns an instance of the class the child tables of this class have
	 * this is used to create them when requested by the table() method.
	 * 
	 * @abstract
	 * @return string Class of the table
	 */
	abstract public function getTableInstance(DB$db, $tablename, Model$model);

}