<?php

namespace spitfire\io\html;


class HTMLTextArea extends HTMLElement
{
	private $text = null;
	
	public function __construct($type, $name, $value, $id = null) {
		$this->setParameter('type',  $type);
		$this->setParameter('name',  $name);
		$this->text = $value;
		
		if (is_null($id)) {
			$this->setParameter('id', 'field_' . $name);
		}
		else {
			$this->setParameter('id', $id);
		}
	}
	
	public function getContent() {
		return $this->text;
	}
	
	public function setValue($value) {
		$this->text = $value;
	}
	
	public function getValue() {
		return $this->text;
	}

	public function getTag() {
		return 'textarea';
	}	
}