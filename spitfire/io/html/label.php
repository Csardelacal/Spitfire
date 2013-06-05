<?php

namespace spitfire\io\html;


class HTMLLabel extends HTMLElement
{
	private $caption = null;
	
	public function __construct(HTMLInput$input, $caption) {
		$this->setParameter('for', $input->getParam('id'));
		$this->caption = $caption;
	}
	
	public function getContent() {
		return $this->caption;
	}
	
	public function getCaption() {
		return $this->caption;
	}
	
	public function setCaption($text) {
		$this->caption = $text;
	}

	public function getTag() {
		return 'label';
	}	
}