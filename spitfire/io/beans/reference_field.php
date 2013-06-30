<?php

namespace spitfire\io\beans;

use \privateException;

class ReferenceField extends BasicField 
{
	
	public function getDefaultValue() {
		return parent::getDefaultValue();
	}
	
	public function getRequestValue() {
		
		try {
			$v = parent::getRequestValue();
		}
		catch (privateException$e) {
		
			if ($this->getBean()->getParent()) {

				#Check if the model this 'references to' is the parent.
				#In that case the value of this is automatically set.
				$parent_model = $this->getBean()->getParent()->getField()->getModel();
				$this_model   = $this->getField()->getTarget();
				if ($parent_model == $this_model) {
					return $this->getBean()->getParent()->getBean()->getRecord();
				}

			}

			else {
				throw $e;
			}
		}
		
		$reference = $this->getField()->getTarget();
		$table     = $reference->getTable();
		return $table->getById($v);
		
	}
	
	public function getVisibility() {
		$visibility = parent::getVisibility();
		
		if ($this->getBean()->getParent() && $this->getField()->getTarget() == $this->getBean()->getParent()->getField()->getModel())
			return $visibility - \CoffeeBean::VISIBILITY_FORM;
		else 
			return $visibility;
	}
	
}