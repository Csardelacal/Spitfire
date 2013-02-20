<?php

namespace spitfire\model\defaults;

use Model;

class userModel extends Model
{
	
	public function __construct() {
		parent::__construct();
		$this->field('username', 'StringField', 20);
		$this->field('password', 'StringField', 40);
		$this->field('email',    'StringField', 40);
		$this->field('admin',    'IntegerField');
	}
}