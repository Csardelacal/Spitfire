<?php

class UserBean extends CoffeeBean
{
	
	protected $method = "GET";
	
	protected $fields = Array(
	    'unique'  => 'username',
	    'content' => 'surname',
	    'id'      => 'age'
	);
	
	public function validateId($id) {
		return !!(int)$id;
	}
	
}