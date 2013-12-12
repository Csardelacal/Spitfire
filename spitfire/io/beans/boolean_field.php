<?php

namespace spitfire\io\beans;

use \privateException;
use spitfire\io\renderers\RenderableFieldBoolean;

class BooleanField extends BasicField implements RenderableFieldBoolean
{
	
	public function getRequestValue() {
		if ($_SERVER['REQUEST_METHOD'] !== 'POST'
				  || !$this->hasPostData()) {throw new privateException(spitfire()->Log("Not POSTed"));}
		
		try {
			return !!parent::getRequestValue();
		} catch(privateException $e) {
			return false;
		}
	}

	public function getEnforcedFieldRenderer() {
		return null;
	}

	public function getPostTargetFor($name) {
		return null;
	}

	public function validate() {
		//TODO: Validation should be handled by the DB fields
	}

}