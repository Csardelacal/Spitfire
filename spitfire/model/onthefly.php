<?php

class OTFModel extends Schema
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