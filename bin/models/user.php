<?php

class userModel extends Model
{
	
	public function __construct() {
		
		parent::__construct();
		
		$this->field('username', 'StringField', 100);
		$this->field('email',    'StringField', 100);
		$this->field('age',      'StringField', 100);
	
	}
	
}
