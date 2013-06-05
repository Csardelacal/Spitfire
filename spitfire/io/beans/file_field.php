<?php

namespace spitfire\io\beans;

class FileField extends BasicField 
{
	
	private $upload = null;
	
	public function getValue() {
		if     ($this->upload) return $this->upload;
		elseif (!empty($_FILES[$this->getName()]) && $_FILES[$this->getName()]['error'] == 0) $file = $_FILES[$this->getName()]['tmp_name'];
		elseif (parent::getValue()) return parent::getValue();
		else return '';
		
		if (!is_dir('bin/usr/uploads/')) {
			if (!mkdir('bin/usr/uploads/')) throw new privateException('Upload directory does not exist and could not be crated');
		}
		elseif (!is_writable('bin/usr/uploads/')) {
			throw new privateException('Upload directory is not writable');
		}
		
		$filename = 'bin/usr/uploads/' . base_convert(time(), 10, 32) . '_' . base_convert(rand(), 10, 32) . '_' . $_FILES[$this->getName()]['name'];
		
		move_uploaded_file($file, $filename);
		return $this->upload = $filename;
	}
	
	
	public function __toString() {
		$id = "field_{$this->getName()}";
		return sprintf('<div class="field"><label for="%s">%s</label><input type="%s" id="%s" name="%s" ><small>%s</small></div>',
			$id, $this->getCaption(), $this->type, $id, $this->getName(), __(end(explode('/', $this->getValue()))) 
			);
	}
	
}