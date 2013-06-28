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
		return (!$this->name)? $this->field->getName() : $this->name;
	}
	
	public function getPostId() {
		
		$name = $this->getName();
		
		if ($this->getBean()->getParent()) {
			$record = $this->getBean()->getRecord();
			
			if (!is_null($record)) {
				$id = implode(':', $record->getPrimaryData());
				return $this->getBean()->getParent()->getName() . "[$id][$name]";
			}
			else {
				return $this->getBean()->getParent()->getName() . "[_new_{$this->getBean()->getId()}][$name]";
			}
		}
		return $name;
	}
	
	public function setCaption($caption) {
		$this->caption = $caption;
		return $this;
	}
	
	/**
	 * Returns the field this one represents on the model. This provides the field
	 * and the renderers with information about the data it can contain.
	 * 
	 * @return \spitfire\model\Field
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
		$postdata = $this->getBean()->getPostData();
		$name = ($this->name)? $this->name : $this->getField()->getName();
		
		if (!empty($postdata[$name])) return $postdata[$name];
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
