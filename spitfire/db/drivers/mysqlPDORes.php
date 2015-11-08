<?php

namespace spitfire\storage\database\drivers;

use PDO;
use Reference;
use ChildrenField;

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
		if (!$data) return null;
		$data = array_map( Array($this->table->getDB(), 'convertIn'), $data);
		
		#Once the data is clean parse it in
		$_record = Array();
		$fields  = $this->table->getModel()->getFields();
		
		/*foreach ($fields as $field) {
			
			if ($field instanceof Reference) {
				$physical = $field->getPhysical();
				
				#If the primary key of the parent only has 1 field we pass it through
				#a cachable query via getbyid
				if (count($physical) == 1) {
					$query = $field->getTarget()->getTable()->hitCache($data[reset($physical)->getName()]);
				}
				
				if ($query == null) {
					$query    = $this->table->getDb()->table($field->getTarget())->getAll();

					foreach ($physical as $physical_field) {
						$query->addRestriction($physical_field->getReferencedField()->getName(), $data[$physical_field->getName()]);
					}
				}
				
				$_record[$field->getName()] = $query;
			}
			
			elseif ($field instanceof ChildrenField) {
				
			}
			
			else {
				$phys = $field->getPhysical();
				$_record[$field->getName()] = $data[array_shift($phys)->getName()];
			}
			
		}/**/
		$_record = $data;
		
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
	
	public function __destruct() {
		//$this->result->closeCursor();
	}
}