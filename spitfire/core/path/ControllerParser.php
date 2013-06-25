<?php

namespace spitfire\path;

use spitfire\Request;
use spitfire\environment;

class ControllerParser implements PathParser
{
	
	private $request;
	
	public function parseElement($element) {
		
		$app = $this->request->getApp();

		/* To get the controller and action of an element we 
		 * keep checking if each element is a valid controller,
		 * once it didn't find a valid controller it stops.
		 */
		if ($app->hasController($element)){
			$controllerName = $element;
			$this->request->setController( $app->getController($controllerName) );
			return true;
		} else {
			$controller = $app->getController(environment::get('default_controller'));
			$this->request->setController($controller);
			return false;
		}
	}

	public function setRequest(Request $request) {
		$this->request = $request;
	}
	
}