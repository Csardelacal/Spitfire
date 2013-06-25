<?php

namespace spitfire\path;

use spitfire\Request;

class AppParser implements PathParser
{
	
	private $request;
	
	public function parseElement($element) {
		
		$_return = false;
		
		if (spitfire()->appExists($element)) {
			$namespace = $element;
			$_return = true;
		}
		else $namespace = '';
		
		$app = spitfire()->getApp($namespace);
		$this->request->setApp($app);	
		
		return $_return;
	}

	public function setRequest(Request $request) {
		$this->request = $request;
	}
	
}