<?php

class Time
{
	
	/**
	 * 
	 * @fixme lang() died and is no longer usable.
	 * @param type $time
	 * @param type $to
	 * @return type
	 */
	public static function relative($time, $to = null) {
		$to = ($to === null)? time() : $to;
		$lang = lang();
		$diff = $to - $time;
		
		if ($diff > 0) {
			if (1 < $ret = (int)($diff / (3600*24*365))) { return $lang->say('yearsago', $ret); }
			if (1 < $ret = (int)($diff / (3600*24*30)))  { return $lang->say('monthsago', $ret); }
			if (1 < $ret = (int)($diff / (3600*24*7)))   { return $lang->say('weeksago', $ret); }
			if (1 < $ret = (int)($diff / (3600*24)))     { return $lang->say('daysago', $ret); }
			if (1 < $ret = (int)($diff / (3600)))        { return $lang->say('hoursago', $ret); }
			if (1 < $ret = (int)($diff / (60)))          { return $lang->say('minutesago', $ret); }
			return $lang->say('secondsago', $ret); 
		}
	}
	
}