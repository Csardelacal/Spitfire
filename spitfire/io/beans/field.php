<?php

namespace spitfire\io\beans;

use CoffeeBean;
use privateException;

abstract class Field
{
	private $bean;
	private $name;
	private $caption;
	private $model_field;
	private $visibility = 3;
	
	public function __construct(CoffeeBean$bean, $name, $caption) {
		$this->bean = $bean;
		$this->name = $name;
		$this->caption = $caption;
	}
	
	public function setBean(CoffeeBean$bean) {
		$this->bean = $bean;
		return $this;
	}
	
	public function getBean() {
		return $this->bean;
	}
	
	public function setName($name) {
		$this->name = $name;
		return $this;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function setCaption($caption) {
		$this->caption = $caption;
		return $this;
	}
	
	public function getCaption() {
		return $this->caption;
	}
	
	public function getValue() {
		try {
			return $this->getRequestValue();
		}
		catch (privateException $e) {
			return $this->getDefaultValue();
		}
	}
	
	public function getRequestValue() {
		if     (!empty($_POST[$this->name])) return $_POST[$this->name];
		elseif (!empty($_GET[$this->name]) ) return $_GET[$this->name];
		else throw new privateException('Field ' . $this->name . ' was not sent with request');
	}
	
	public function getDefaultValue() {
		return $this->bean->getRecord()->{$this->getModelField()};
	}
	
	public function setModelField($name) {
		$this->model_field = $name;
		return $this;
	}
	
	public function getModelField() {
		return $this->model_field;
	}
	
	public function setVisibility($visibility) {
		if ($visibility >= 0 && $visibility <= 3) $this->visibility = $visibility;
		return $this;
	}
	
	public function getVisibility() {
		return $this->visibility;
	}
	
	public function __toString() {
		$id = "field_{$this->name}";
		return sprintf('<div class="field"><label for="%s">%s</label><input type="%s" id="%s" name="%s" value="%s" ></div>',
			$id, $this->caption, $this->type, $id, $this->name, $this->getValue() 
			);
	}
}
