<?php

class adultModel extends userModel
{
	
	public function getBaseRestrictions() {
		$query = db()->table('user')->getAll()->addRestriction('age', 18, '>');
		return $query->getRestrictions();
	}


	public function getTableName() {
		return 'user';
	}
	
}