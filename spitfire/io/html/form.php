<?php

namespace spitfire\io\html;

use \CoffeeBean;

class HTMLForm extends HTMLElement
{
	
	public $submit = '<input type="submit">';
	
	public function __construct($action) {
		$this->setParameter('action',  $action);
		$this->setParameter('method',  'POST');
		$this->setParameter('enctype', 'multipart/form-data');
	}
	
	public function getChildren() {
		$ret = parent::getChildren();
		$ret[] = $this->submit;
		return $ret;
	}

	public function getTag() {
		return 'form';
	}
}