<?php

class dependantModel extends Model
{
	
	protected $test;
	protected $title;
	protected $content;
	
	public function __construct() {
		
		parent::__construct();
		
		$this->test    = new IntegerField();
		$this->title   = new StringField(100);
		$this->content = new TextField();
		
		$this->title->setUnique(true);
		
		$this->reference('test');
	
	}
	
}