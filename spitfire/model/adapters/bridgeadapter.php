<?php namespace spitfire\model\adapters;

use \ManyToManyField;
use spitfire\Model;

class BridgeAdapter
{
	private $parentField;
	private $leftField;
	private $rightField;
	private $leftValue;
	private $rightValue;
	
	public function __construct(ManyToManyField$parentField, $value1, $value2) {
		$this->parentField = $parentField;
		$this->makeFields();
		$this->addValue($value1);
		$this->addValue($value2);
	}
	
	public function makeFields() {
		$bridge = $this->parentField->getBridge();
		$fields = $bridge->getFields();
		$this->leftField = reset($fields);
		$this->rightField = end($fields);
	}
	
	public function addValue(Model$value) {
		$schema = $value->getTable()->getModel();
		if ($this->leftField->getTarget() === $schema && !$this->leftValue) {
			$this->leftValue = $value;
		}
		elseif ($this->rightField->getTarget() === $schema && !$this->rightValue) {
			$this->rightValue = $value;
		}
	}
	
	public function makeRecord() {
		$record = $this->parentField->getBridge()->getTable()->newRecord();
		$record->{$this->leftField} = $this->leftValue;
		$record->{$this->rightField} = $this->rightValue;
		return $record;
	}
}
