<?php

namespace spitfire\io\html;

class HTMLDiv extends HTMLElement
{
	
	public function __construct() {
		$args = func_get_args();
		
		foreach ($args as $arg) {
			if (is_array($arg)) {
				foreach ($arg as $param => $v) 
					$this->setParameter ($param, $v);
			}
			else {
				$this->addChild ($arg);
			}
		}
	}

	public function getTag() {
		return 'div';
	}
}