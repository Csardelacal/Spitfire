<?php

class testModel extends Model
{
	
	public function __construct() {
		
		//parent::__construct();
		$this->field('id1', 'IntegerField')
			->setPrimary(true)
			->setAutoIncrement(true);
		
		$this->field('id2', 'IntegerField')
			->setPrimary(true);
		
		$this->field('id3', 'IntegerField');
		
		$this->field('content', 'StringField', 100);
		
		$this->field('image', 'FileField');
	
	}
	
}
