<?php

namespace spitfire\io\beans;

use CoffeeBean;
use privateException;

abstract class Field
{
	
	private $bean;
	private $name;
	private $field;
	private $caption;
	private $visibility = 3;
	
	public function __construct(CoffeeBean$bean, $field, $caption) {
		$this->bean = $bean;
		$this->field = $field;
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
		
		$name = (!$this->name)? $this->field->getName() : $this->name;
		
		if ($this->getBean()->getParent()) {
			$record = $this->getBean()->getRecord();
			
			if (!is_null($record)) {
				$id = implode(':', $record->getPrimaryData());
				return $this->getBean()->getName() . "[$id][$name]";
			}
			else {
				return $this->getBean()->getName() . "[_new_{$this->getBean()->getId()}][$name]";
			}
		}
		return $name;
	}
	
	public function setCaption($caption) {
		$this->caption = $caption;
		return $this;
	}
	
	/**
	 * @todo Document
	 * @return spitfire\model\Field
	 */
	public function getField() {
		return $this->field;
	}
	
	public function getFieldName() {
		return $this->field->getName();
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
		if     (!empty($_POST[$this->getName()])) return $_POST[$this->getName()];
		elseif (!empty($_GET[$this->getName()]) ) return $_GET [$this->getName()];
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
