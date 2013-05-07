<?php

class TestBean extends CoffeeBean
{
	
	public $model = 'test';
	
	public function __construct() {
		$this->field('TextField', 'id1', 'Id #1')
			->setModelField('id1');
		
		$this->field('TextField', 'id2', 'Id #2')
			->setModelField('id2');
		
		$this->field('TextField', 'id3', 'Id #3')
			->setModelField('id3');
		
		$this->field('TextField', 'content', 'Content')
			->setModelField('content')
			->setVisibility(CoffeeBean::VISIBILITY_FORM);
		
		$this->field('FileField', 'image', 'image')
			->setModelField('image')
			->setVisibility(CoffeeBean::VISIBILITY_FORM);
	}
	
}