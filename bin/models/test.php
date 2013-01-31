<?php

class testModel extends Model
{
	
	protected $content;
	protected $id1;
	protected $id2;
	
	public function __construct() {
		
		//parent::__construct();
		
		$this->id1 = new IntegerField();
		$this->id2 = new IntegerField();
		$this->id1->setPrimary(true);
		$this->id1->setAutoIncrement(true);
		
		$this->content = new StringField(100);
	
	}
	
}