<?php namespace spitfire\model\adapters;

use Model;
use privateException;

class ReferenceAdapter extends BaseAdapter
{
	
	public function usrSetData($data) {
		//Check if the incoming data is an int
		if ( !$data instanceof Model ) {
			throw new privateException('This adapter only accepts models');
		}
		//Make sure the finally stored data is an integer.
		parent::usrSetData($data);
	}
}

