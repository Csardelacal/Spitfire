<?php

namespace spitfire\model\defaults;

use Model;

class userModel extends Model
{
	
	public function definitions() {
		$this->username = new \StringField(20);
		$this->password = new \StringField(40);
		$this->email    = new \StringField(40);
		$this->admin    = new \IntegerField();
	}
}