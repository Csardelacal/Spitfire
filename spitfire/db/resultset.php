<?php

namespace spitfire\storage\database\drivers;

use databaseRecord;

interface resultSetInterface
{
	/**
	 * Fetches data from a driver's resultset.
	 * 
	 * @return databaseRecord A record of a database.
	 */
	public function fetch();
	public function fetchArray();
	public function fetchAll(databaseRecord$parent = null);
}