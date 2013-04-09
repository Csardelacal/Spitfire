<?php

class UserBean extends CoffeeBean
{
	
	public $model = 'user';
	
	public function __construct() {
		$this->field('TextField', 'user', 'User Name')
			->setModelField('username');
		
		$this->field('TextField', 'email', 'Enter email')
			->setModelField('email');
		
		$this->field('TextField', 'age', 'Enter your age')
			->setModelField('age')
			->setVisibility(CoffeeBean::VISIBILITY_FORM);
	}
	
	public function validateEmail($email) {
		if (strlen($email) < 5) {
			$this->validationError("Email is too short");
			return false;
		}
		
		return true;
	}
	
}