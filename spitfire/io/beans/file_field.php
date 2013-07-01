<?php

namespace spitfire\io\beans;

class FileField extends BasicField 
{
	
	private $upload = null;
	
	public function getRequestValue() {
		$file = parent::getRequestValue();
		
		if ($file instanceof \spitfire\io\Upload) return $file->store();
		else throw new privateException('Not an upload');
	}
	
	public function __toString() {
		$id = "field_{$this->getName()}";
		return sprintf('<div class="field"><label for="%s">%s</label><input type="%s" id="%s" name="%s" ><small>%s</small></div>',
			$id, $this->getCaption(), $this->type, $id, $this->getName(), __(end(explode('/', $this->getValue()))) 
			);
	}
	
}