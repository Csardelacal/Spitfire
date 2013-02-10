<?php

namespace spitfire\io\beans;

use CoffeeBean;

abstract class Field
{
	private $method;
	private $name;
	private $caption;
	private $value;
	private $model_field;
	
	protected $type = 'text';
	
	public function __construct($name, $caption, $method = CoffeeBean::METHOD_POST) {
		$this->name = $name;
		$this->caption = $caption;
		$this->method = $method;
	}
	
	public function setName($name) {
		$this->name = $name;
		return $this;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function setMethod($method) {
		$this->method = $method;
		return $this;
	}
	
	public function getMethod() {
		return $this->method;
	}
	
	public function setCaption($caption) {
		$this->caption = $caption;
		return $this;
	}
	
	public function getCaption() {
		return $this->caption;
	}
	
	public function setValue($value) {
		$this->value = $value;
		return $this;
	}
	
	public function getValue() {
		return $this->value;
	}
	
	public function setModelField($name) {
		$this->model_field = $name;
		return $this;
	}
	
	public function getModelField() {
		return $this->model_field;
	}
	
	public function __toString() {
		$id = "field_{$this->name}";
		return sprintf('<div class="field"><label for="%s">%s</label><input type="%s" id="%s" name="%s" ></div>',
			$id, $this->caption, $this->type, $id, $this->name 
			);
	}
}
