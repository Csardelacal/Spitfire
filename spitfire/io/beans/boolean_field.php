<?php

namespace spitfire\io\beans;

use spitfire\exceptions\PrivateException;
use spitfire\io\renderers\RenderableFieldBoolean;

class BooleanField extends BasicField implements RenderableFieldBoolean
{
	
	public function getRequestValue() {
		if ($_SERVER['REQUEST_METHOD'] !== 'POST'
				  || !$this->hasPostData()) {throw new PrivateException(spitfire()->Log("Not POSTed"));}
		
		try {
			return !!parent::getRequestValue();
		} catch(PrivateException $e) {
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