<?php

class testModel extends Model
{
	
	public function __construct() {
		parent::__construct();
		$this->field('content', 'TextField');
	}
	
}