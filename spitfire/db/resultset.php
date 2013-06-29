<?php

namespace spitfire\storage\database\drivers;

use Model;

interface resultSetInterface
{
	/**
	 * Fetches data from a driver's resultset.
	 * 
	 * @return Model A record of a database.
	 */
	public function fetch();
	public function fetchArray();
	public function fetchAll(Model$parent = null);
}