<?php

namespace spitfire\io\beans;

class BasicField extends Field
{
	private $model_field;
	
	public function setModelField($name) {
		$this->model_field = $name;
		return $this;
	}
	
	public function getModelField() {
		return $this->model_field;
	}
}