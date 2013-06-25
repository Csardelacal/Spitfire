<?php

namespace spitfire\path;

use spitfire\Request;
use spitfire\environment;

class ActionParser implements PathParser
{
	
	private $request;
	
	public function parseElement($element) {
		
		$controller = $this->request->getController();
		
		if (is_callable(Array($controller, $element))) {
			$this->request->setAction($element);
			return true;
		}
		elseif (!$element) {
			$this->request->setAction(environment::get('default_action'));
			return false;
		}
		else {
			throw new \publicException('Action not Found', 404);
		}
		
	}

	public function setRequest(Request $request) {
		$this->request = $request;
	}
	
}