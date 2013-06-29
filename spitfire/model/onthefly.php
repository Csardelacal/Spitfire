<?php

class OTFModel extends ModelMeta
{
	
	private $name;
	
	public function __construct() {}
	
	public function setName($name) {
		$this->name = $name;
	}
	
	public function getName() {
		return $this->name;
	}

	public function definitions() {
		return;
	}
	
}