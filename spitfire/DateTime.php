<?php namespace spitfire;

class DateTime extends \DateTime {
	
	public function __toString() {
		return $this->format(environment::get('datetime.format'));
	}
	
}