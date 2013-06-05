<?php

namespace spitfire\io\beans\renderers;

use spitfire\io\beans\BasicField;
use spitfire\io\beans\TextField;
use spitfire\io\beans\FileField;
use spitfire\io\html\HTMLInput;
use spitfire\io\html\HTMLLabel;
use spitfire\io\html\HTMLDiv;

class SimpleFieldRenderer {
	
	public function renderForm($field) {
		if ($field instanceof BasicField) {
			return $this->renderBasicField($field);
		}
		else return $field;
		//TODO: Do something real here
	}
	
	public function renderList($field) {
		return __(strip_tags(strval($field)), 100);
	}
	
	public function renderBasicField($field) {
		if ($field instanceof TextField) {
			$input = new HTMLInput('text', $field->getName(), $field->getValue());
			$label = new HTMLLabel($input, $field->getCaption());
			return new HTMLDiv($label, $input, Array('class' => 'field'));
		}
		elseif ($field instanceof FileField) {
			$input = new HTMLInput('file', $field->getName(), $field->getValue());
			$label = new HTMLLabel($input, $field->getCaption());
			return new HTMLDiv($label, $input, Array('class' => 'field'));
		}
		//TODO: Add more options
		else return $field;
	}
	
	public function renderReferencedField($field) {
		
	}
	
	public function renderChildBean($field) {
		
	}
}