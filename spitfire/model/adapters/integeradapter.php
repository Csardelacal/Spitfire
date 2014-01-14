<?php namespace spitfire\model\adapters;

/**
 * This adapter is in charge of ensuring that only integers reach the database
 * channeled through it. So, whenever the user sets data to it we will perform
 * a check to see if it's Integer data. Otherwise they'll get an exception.
 */
class IntegerAdapter extends BaseAdapter
{
	/**
	 * This adapter verifies that anything that reaches it is a valid integer value,
	 * otherwise it will throw an exception and stop any potentially unsafe 
	 * operation.
	 * 
	 * @param int $data
	 * @throws \privateException
	 */
	public function usrSetData($data) {
		//Check if the incoming data is an int
		if ( (int)$data != $data && !is_int($data) ) {
			throw new \privateException('This adapter only accepts integers');
		}
		//Make sure the finally stored data is an integer.
		parent::usrSetData((int)$data);
	}
}

