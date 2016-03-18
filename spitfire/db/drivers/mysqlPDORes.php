<?php

namespace spitfire\storage\database\drivers;

use PDO;

/**
 * This class works as a traditional resultset. It acts as an adapter between the
 * driver's raw data retrieving and the logical record classes.
 * 
 * @author César de la Cal <cesar@magic3w.com>
 */
class mysqlPDOResultSet implements resultSetInterface
{
	/**
	 * Contains the raw pointer that PDO has created when executing the query.
	 * This allows spitfire to retrieve all the data needed to create a complete
	 * database record.
	 *
	 * @var PDOStatement
	 */
	private $result;
	
	/**
	 * This is a reference to the table this resultset belongs to. This allows
	 * Spitfire to retrieve data about the model and the fields the datatype has.
	 *
	 * @var spitfire\storage\database\Table 
	 */
	private $table;
	
	public function __construct(MysqlPDOTable$table, $stt) {
		$this->result = $stt;
		$this->table = $table;
	}

	public function fetch() {
		$data = $this->result->fetch(PDO::FETCH_ASSOC);
		#If the data does not contain anything we return a null object
		if (!$data) { return null; }
		$_record = array_map( Array($this->table->getDB(), 'convertIn'), $data);
		
		$record = $this->table->newRecord($_record);
		$this->table->cache($record);
		return $record;
	}

	public function fetchAll() {
		//TODO: Swap to fatch all
		//$data = $this->result->fetchAll(PDO::FETCH_ASSOC);
		$_return = Array();
		
		while ($data = $this->fetch()) {
			$_return[] = $data;
		}
		return $_return;
	}
	

	public function fetchArray() {
		return $this->result->fetch(PDO::FETCH_ASSOC);
	}
	
}