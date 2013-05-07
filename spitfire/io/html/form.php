<?php

namespace spitfire\io\html;

use \CoffeeBean;

class HTMLForm extends HTMLElement
{
	private $method = 'POST';
	private $action;
	private $fields;
	
	public function __construct($action, CoffeeBean$bean = null) {
		$this->action = $action;
		$this->fields = $bean->getFields();
	}

	public function getContent() {
		$content = $this->fields;
		$content[] = '<input type="submit">';
		return implode("\n", $content);
	}

	public function getParams() {
		return Array('action' => $this->action, 'method' => $this->method, 'enctype' => 'multipart/form-data');
	}

	public function getTag() {
		return 'form';
	}

	public function getChildren() {
		return $this->fields;
	}
}