<?php

namespace spitfire\io\beans;

use \privateException;

class BooleanField extends BasicField 
{
	
	public function getRequestValue() {
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new privateException(spitfire()->Log("Not POSTed"));
		
		try {
			parent::getRequestValue();
			spitfire()->Log("Boolean true");
			return true;
		}
		catch(privateException $e) {
			spitfire()->Log("Boolean false");
			return false;
		}
	}
	
}