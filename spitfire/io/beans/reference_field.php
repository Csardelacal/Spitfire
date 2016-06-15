<?php namespace spitfire\io\beans;

use spitfire\exceptions\PrivateException;
use spitfire\io\renderers\RenderableFieldSelect;

class ReferenceField extends BasicField implements RenderableFieldSelect
{
	
	public function getDefaultValue() {
		return parent::getDefaultValue();
	}
	
	public function getRequestValue() {
		
		try {
			$v = parent::getRequestValue();
		}
		catch (PrivateException$e) {
		
			if ($this->getBean()->getParent()) {

				#Check if the model this 'references to' is the parent.
				#In that case the value of this is automatically set.
				$parent_model = $this->getBean()->getParent()->getField()->getModel();
				$this_model   = $this->getField()->getTarget();
				if ($parent_model === $this_model) {
					return $this->getBean()->getParent()->getBean()->getRecord();
				}

			}

			throw $e;
		}
		
		$reference = $this->getField()->getTarget();
		$table     = $reference->getTable();
		return $table->getById($v);
		
	}
	
	public function getVisibility() {
		$visibility = parent::getVisibility();
		
		if ($this->getBean()->getParent() && $this->getField()->getTarget() === $this->getBean()->getParent()->getField()->getModel())
			return $visibility - \CoffeeBean::VISIBILITY_FORM;
		else 
			return $visibility;
	}

	public function getEnforcedFieldRenderer() {
		return null;
	}

	public function getOptions() {
		$opts = $this->getField()->getTarget()->getTable()->getAll()->fetchAll();
		$_return = Array();
		
		foreach ($opts as $opt) {$_return[implode(':', $opt->getPrimaryData())] = strval($opt);}
		
		return $_return;
	}

	public function getPartial($str) {
		$opts    = $this->getField()->getTarget()->getTable()->get(null, $str)->fetchAll();
		$_return = Array();
		
		foreach ($opts as $opt) {$_return[implode(':', $opt->getPrimaryData())] = strval($opt);}
		
		return $_return;
	}

	public function getSelectCaption($id) {
		return $this->getField()->getTarget()->getTable()->getById($id);
	}

	public function getSelectId($caption) {
		$record = $this->getField()->getTarget()->getTable()->get(null, $str)->fetch();
		return implode(':', $record->getPrimaryData());
	}

}