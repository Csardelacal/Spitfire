<?php

namespace spitfire\io\beans;

class BasicField extends Field
{
	private $model_field;
	
	public function getDefaultValue() {
		if ($this->getBean()->getRecord()) {
			return $this->getBean()->getRecord()->{$this->getModelField()};
		}
		else {
			return null;
		}
	}
	
	public function setModelField($name) {
		$this->model_field = $name;
		return $this;
	}
	
	public function getModelField() {
		return $this->getFieldName();
	}

	public function getPostTargetFor($name) {
		return null;
	}

}