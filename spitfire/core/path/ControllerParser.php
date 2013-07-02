<?php

namespace spitfire\path;

use \Exception;
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
		try {
			$this->request->setController( $app->getController($element) );
			return true;
		} catch (Exception $e) {
			$controller = $app->getController(environment::get('default_controller'));
			$this->request->setController($controller);
			return false;
		}
	}

	public function setRequest(Request $request) {
		$this->request = $request;
	}
	
}