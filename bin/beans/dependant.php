<?php

class DependantBean extends CoffeeBean
{
	
	public $model = 'dependant';
	
	public function __construct() {
		$this->field('ReferenceField', 'test', 'Test', CoffeeBean::METHOD_GET)
			->setModelField('test');
		
		$this->field('TextField', 'content', 'Enter content', CoffeeBean::METHOD_GET)
			->setModelField('content');
		
		$this->field('TextField', 'content2', 'Enter your second content', CoffeeBean::METHOD_GET)
			->setModelField('content')
			->setVisibility(CoffeeBean::VISIBILITY_FORM);
	}
	
	
}