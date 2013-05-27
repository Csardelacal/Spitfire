<?php

namespace spitfire\io\beans;

use \databaseRecord;
use Exception;
use Model;

class ReferenceField extends Field 
{
	
	public function getValue() {
		
		$v = parent::getValue();
		
		
		if ($v instanceof databaseRecord) {
			return $v;
		}
		else {
			$reference = Model::getInstance($this->getBean()->model)->getField($this->getModelField());
			$table     = db()->table($reference->getTarget());
			$pk        = $table->getPrimaryKey();
			$search    = explode('|', $v);
			$query     = $table->get(array_shift($pk), array_shift($search));
			
			if (count($pk)) {
				$query->addRestriction(array_shift($pk), array_shift($search));
			}
			
			return $query->fetch();
		}
	}
	
	public function setValue($value) {
		
		if ($value instanceof databaseRecord) {
			$reference = Model::getInstance($this->getBean()->model)->getField($this->getModelField());
			if ($value->getTable()->getModel() == $reference->getTarget() ) {
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
	
	public function __toString() {
		try {
			$reference = Model::getInstance($this->getBean()->model)->getField($this->getModelField());
			
			
			$query = db()->table($reference->getTarget())->getAll();
			$query->setPage(-1);
			$possibilities = $query->fetchAll();
			$active = (!$this->getValue())? null : $this->getValue()->getPrimaryData();
			$str = '';

			foreach($possibilities as $pos) {
				$selected = ($active == $pos->getPrimaryData())? 'selected' : '';

				$str.= sprintf('<option value="%s" %s>%s</option>' . "\n", 
					implode('|', $pos->getPrimaryData()), 
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