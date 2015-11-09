<?php

namespace spitfire\model\defaults;

use spitfire\Model;
use spitfire\storage\database\Schema;

class userModel extends Model
{
	
	public function definitions(Schema$schema) {
		$schema->username = new \StringField(20);
		$schema->password = new \StringField(40);
		$schema->email    = new \StringField(40);
		$schema->admin    = new \IntegerField();
	}
}