<?php

namespace spitfire\io\html;


class HTMLSelect extends HTMLElement
{
	private $selected = null;
	
	public function __construct($name, $value, $id = null) {
		$this->setParameter('name',  $name);
		
		$this->selected = $value;
		
		if (is_null($id)) {
			$this->setParameter('id', 'field_' . $name);
		}
		else {
			$this->setParameter('id', $id);
		}
	}
	
	public function getChildren() {
		$children = parent::getChildren();
		
		foreach ($children as $child) {
			if ($child instanceof HTMLOption) {
			if ($child->getParam('value') == $this->selected || $child == $this->selected) 
				$child->setParameter('selected', 'selected');
			}
		}
		
		return $children;
	}

	public function getTag() {
		return 'select';
	}
}