<?php

class dependantModel extends _SF_DBTable
{
	
	protected $tablename = 'dependant';
	
	public function validateTest_id($value) {
		if ($value < 0) $this->errorMsg ('Value is nagative');
		if ($value < 0) $this->errorMsg ('Value is nagative');
		return $value > 0;
	}
	
}