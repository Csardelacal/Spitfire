<?php

namespace spitfire\io\beans;

use spitfire\io\renderers\RenderableFieldInteger;

class IntegerField extends BasicField implements RenderableFieldInteger
{
	public function getEnforcedFieldRenderer() {
		return null;
	}

}