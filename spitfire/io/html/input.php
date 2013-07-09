<?php

namespace spitfire\io\html;


class HTMLInput extends HTMLUnclosedElement
{
	
	public function __construct($type, $name, $value = null, $id = null) {
		$this->setParameter('type',  $type);
		$this->setParameter('name',  $name);
		$this->setParameter('value', $value);
		
		if (is_null($id)) {
			$this->setParameter('id', 'field_' . $name);
		}
		else {
			$this->setParameter('id', $id);
		}
	}

	public function getTag() {
		return 'input';
	}	
}