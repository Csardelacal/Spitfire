<?php namespace spitfire\model\adapters;

use Model;
use privateException;

class ReferenceAdapter extends BaseAdapter
{
	private $query;
	
	public function dbSetData($data) {
		$table = $this->getField()->getTarget()->getTable();
		$query = $table->getAll();
		$physical = $this->getField()->getPhysical();
		
		foreach ($physical as $p) {
			/* @var $p \spitfire\storage\database\DBField */
			$query->addRestriction($p->getReferencedField()->getName(), $data[$p->getName()]);
		}
		
		$this->query = $query;
	}
	
	public function usrGetData() {
		if ($this->query instanceof \spitfire\storage\database\Query) {
			return $this->query = $this->query->fetch();
		} else {
			return $this->query;
		}
	}
	
	public function usrSetData($data) {
		//Check if the incoming data is an int
		if ( !$data instanceof Model && !is_null($data)) {
			throw new privateException('This adapter only accepts models');
		}
		//Make sure the finally stored data is an integer.
		$this->query = $data;
	}
}

