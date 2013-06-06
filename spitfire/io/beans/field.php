<?php

namespace spitfire\io\beans;

use CoffeeBean;
use privateException;

abstract class Field
{
	private $bean;
	private $name;
	private $caption;
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
		if ($this->getBean()->getParent()) {
			$record = implode(':', $this->getBean()->getRecord()->getPrimaryData());
			return $this->getBean()->getName() . "[$record][$this->name]";
		}
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
	
	abstract public function getDefaultValue();
	
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
