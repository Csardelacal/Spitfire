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
	
}