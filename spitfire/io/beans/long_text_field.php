<?php

namespace spitfire\io\beans;

class LongTextField extends Field 
{
	
	
	public function __toString() {
		$id = "field_{$this->getName()}";
		return sprintf('<div class="field"><label for="%s">%s</label><textarea id="%s" name="%s" >%s</textarea></div>',
			$id, $this->getCaption(), $id, $this->getName(), $this->getValue() 
			);
	}
}