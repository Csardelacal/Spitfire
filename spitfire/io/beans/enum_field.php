<?php namespace spitfire\io\beans;

use spitfire\io\renderers\RenderableFieldSelect;

class EnumField extends BasicField implements RenderableFieldSelect
{
	public function getEnforcedFieldRenderer() {
		return null;
	}

	public function getOptions() {
		$options = $this->getField()->getOptions();
		$_return = Array();
		foreach($options as $option) {$_return[$option] = $option;}
		return $_return;
	}

	public function getPartial($str) {
		return $this->getOptions();
	}

	public function getSelectCaption($id) {
		$options = $this->getOptions();
		return $options[$id];
	}

	public function getSelectId($caption) {
		return array_search($caption, $this->getOptions());
	}

}