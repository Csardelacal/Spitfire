<?php

namespace spitfire\io\beans;

use \CoffeeBean;

class FileField extends Field 
{
	
	protected $type = 'file';
	
	public function getValue() {
		if     (!empty($_FILES[$this->getName()])) $file = $_FILES[$this->getName()]['tmp_name'];
		elseif (parent::getValue()) return parent::getValue();
		else return '';
		
		move_uploaded_file($file, 'bin/usr/uploads/' . base_convert(time(), 10, 32) . '_' . base_convert(rand(), 10, 32) . $_FILES[$this->getName()]['name']);
		return 'bin/usr/uploads/' . base_convert(time(), 10, 32) . base_convert(rand(), 10, 32) . $_FILES[$this->getName()]['name'];
	}
	
	
	public function __toString() {
		$id = "field_{$this->getName()}";
		return sprintf('<div class="field"><label for="%s">%s</label><input type="%s" id="%s" name="%s" ><small>%s</small></div>',
			$id, $this->getCaption(), $this->type, $id, $this->getName(), __(end(explode('/', $this->getValue()))) 
			);
	}
	
}