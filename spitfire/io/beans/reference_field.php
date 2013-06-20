<?php

namespace spitfire\io\beans;

use \databaseRecord;
use Exception;
use Model;

class ReferenceField extends BasicField 
{
	
	public function getValue() {
		
		$v = parent::getValue();
		
		
		if ($v instanceof databaseRecord) {
			return $v;
		}
		else {
			$reference = $this->getField()->getTarget();
			$table     = $reference->getTable();
			$pk        = $table->getPrimaryKey();
			$search    = explode(':', $v);
			$query     = $table->get(array_shift($pk), array_shift($search));
			
			while (count($pk)) {
				$query->addRestriction(array_shift($pk), array_shift($search));
			}
			
			return $query->fetch();
		}
	}
	
	public function setValue($value) {
		
		if ($value instanceof databaseRecord) {
			$reference = $this->getField()->getTarget();
			if ($value->getTable()->getModel() == $reference ) {
				parent::setValue($value);
			}
			else throw new \privateException("Incompatible field types");
		}
		else {
			$table  = $reference->getTable();
			parent::setValue($table->getById($value)->fetch());
		}
	}
	
	public function __toString() {
		try {
			$reference = $this->getField()->getTarget();
			
			
			$query = db()->table($reference)->getAll();
			$query->setPage(-1);
			$possibilities = $query->fetchAll();
			$active = (!$this->getValue())? null : $this->getValue()->getPrimaryData();
			$str = '';

			foreach($possibilities as $pos) {
				$selected = ($active == $pos->getPrimaryData())? 'selected' : '';

				$str.= sprintf('<option value="%s" %s>%s</option>' . "\n", 
					implode(':', $pos->getPrimaryData()), 
					$selected,
					$pos
					);
			}


			$id = "field_{$this->getName()}";
			return sprintf('<div class="field"><label for="%s">%s</label><select id="%s" name="%s">%s</select></div>',
				$id, $this->getCaption(), $id, $this->getName(), $str
				);
		} catch (Exception $e) {
			return sprintf('<div class="error"><h1>%s: %s</h1><pre>%s</pre></div>',
				$e->getCode(),
				$e->getMessage(),
				$e->getTraceAsString()
				);
		}
	}
	
}