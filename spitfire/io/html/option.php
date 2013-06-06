<?php

namespace spitfire\io\html;


class HTMLOption extends HTMLElement
{
	private $caption = null;

	public function __construct($value, $caption) {
		$this->caption = $caption;
		$this->setParameter('value', $value);
	}
	
	public function getCaption() {
		return $this->caption;
	}
	
	public function setCaption($text) {
		$this->caption = $text;
	}
	
	public function getContent() {
		return $this->caption;
	}

	public function getTag() {
		return 'option';
	}	
}