<?php

namespace spitfire\io\beans;

class DateTimeField extends BasicField 
{
	public function getRequestValue() {
		$pd = parent::getRequestValue();
		$ts =  mktime($pd['hours'], $pd['minutes'], 0, $pd['month'], $pd['day'], $pd['year']);
		
		return date('Y-m-d H:i:s', $ts);
	}
}