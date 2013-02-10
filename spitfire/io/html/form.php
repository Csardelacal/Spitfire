<?php

namespace spitfire\io\html;

use \CoffeeBean;

class HTMLForm
{
	private $action;
	private $fields;
	
	public function __construct($action, CoffeeBean$bean = null) {
		$this->action = $action;
		$this->fields = $bean->getFields();
	}
	
	public function __toString() {
		return sprintf('<form action="%s" method="POST">%s</form>',
			$this->action, implode('', $this->fields));
	}
}