<?php

namespace spitfire\model\defaults;

use Model;

class userModel extends Model
{
	
	public static function definitions($lst) {
		$lst->username = new \StringField(20);
		$lst->password = new \StringField(40);
		$lst->email    = new \StringField(40);
		$lst->admin    = new \IntegerField();
	}
}