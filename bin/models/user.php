<?php

use spitfire\model\defaults;

class userModel extends defaults\userModel
{
	
	public function __construct() {
		
		parent::__construct();
		$this->field('age',      'StringField', 100);
	
	}
	
}
