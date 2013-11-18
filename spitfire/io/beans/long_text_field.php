<?php namespace spitfire\io\beans;

use spitfire\io\renderers\RenderableFieldText;

class LongTextField extends BasicField implements RenderableFieldText
{
	
	
	public function __toString() {
		$id = "field_{$this->getName()}";
		return sprintf('<div class="field"><label for="%s">%s</label><textarea id="%s" name="%s" >%s</textarea></div>',
			$id, $this->getCaption(), $id, $this->getName(), $this->getValue() 
			);
	}

	public function getEnforcedFieldRenderer() {
		return null;
	}

	public function getMaxLength() {
		return null;
	}

}