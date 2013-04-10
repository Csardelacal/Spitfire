<?php

namespace spitfire\io\beans;

use \databaseRecord;

class ReferenceField extends Field 
{
	
	public function getValue() {
		
		$v = parent::getValue();
		
		
		if ($v instanceof databaseRecord) {
			return $v;
		}
		else {
			$table  = db()->table($this->getModelField());
			$pk     = $table->getPrimaryKey();
			$search = explode('|', $v);
			$query  = $table->get(array_shift($pk), array_shift($search));
			
			if (count($pk)) {
				$query->addRestriction(array_shift($pk), array_shift($search));
			}
			
			return $query->fetch();
		}
	}
	
	public function setValue($value) {
		
		if ($value instanceof databaseRecord) {
			if ($value->getTable()->getModel()->getName() == $this->getModelField()	) {
				parent::setValue($value);
			}
			else throw new \privateException("Incompatible field types");
		}
		else {
			$table  = db()->table($this->getModelField());
			$pk     = $table->getPrimaryKey();
			$search = explode('|', $value);
			$query  = $table->get(array_shift($pk), array_shift($search));
			
			if (count($pk)) {
				$query->addRestriction(array_shift($pk), array_shift($search));
			}
			
			parent::setValue($query->fetch());
		}
	}
	
}