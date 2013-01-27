<?php

use spitfire\storage\database\Table;

class dependantModel extends Table
{
	
	protected $tablename = 'dependant';
	
	public function validateTest_id($value) {
		if ($value < 0) $this->errorMsg ('Value is nagative');
		if ($value < 0) $this->errorMsg ('Value is nagative');
		return $value > 0;
	}
	
}