<?php namespace spitfire\model\adapters;

use DateTime;

/**
 * This class creates an interface between the usual database's string or integer
 * based approach to an object oriented one that allows building apps easier.
 */
class DateTimeAdapter extends BaseAdapter
{
	/**
	 * Converts the DateTime Spitfire manages into a Database friendly version 
	 * (string).
	 * 
	 * @return string
	 */
	public function dbGetData() {
		
		/* @var $datetime DateTime */
		$datetime = parent::dbGetData();
		
		return $datetime->format('Y-m-d H:i:s');
	}
	
	/**
	 * Takes the database string representing date / time and converts it to a 
	 * user friendly DateTime object that allows solid object oriented programming
	 * inside of Spitfire.
	 * 
	 * @param DateTime $data
	 */
	public function dbSetData($data) {
		$data = new DateTime(strtotime($data));
		parent::dbSetData($data);
	}
}