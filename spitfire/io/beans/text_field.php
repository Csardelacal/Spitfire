<?php

namespace spitfire\io\beans;

use spitfire\io\renderers\RenderableFieldString;

class TextField extends BasicField implements RenderableFieldString
{
	public function getEnforcedFieldRenderer() {
		return null;
	}

	public function getMaxLength() {
		return $this->getField()->getLength();
	}

}