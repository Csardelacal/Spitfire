<?php

use spitfire\model\defaults;

class userModel extends defaults\userModel
{
	
	public function __construct() {
		
		parent::__construct();
		$this->field('age',      'StringField', 100);
	
	}
	
	public function validateUsername($username, $table) {
		$ok = true;
		
		if (!$username) {
			$table->errorMsg('Username cannot be empty');
			$ok = false;
		}
		
		if (strlen($username) < 4) {
			$table->errorMsg('Username cannot be shorter than 4 characters');
			$ok = false;
		}
		
		return $ok;
	}
	
}
