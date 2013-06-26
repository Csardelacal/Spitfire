<?php

namespace spitfire\io\html;

class dateTimePicker extends HTMLSpan
{

	private $timestamp;
	private $inputname;
	
	public static $months = Array('January', 'February', 'March', 'April', 'May', 'june', 'July', 'August', 'September', 'October', 'November', 'December');
	
	public function __construct($timestamp) {
		
		if     (!$timestamp)            $this->timestamp = time();
		elseif (is_numeric($timestamp)) $this->timestamp = $timestamp;
		else                            $this->timestamp = strtotime($timestamp);
		
		$this->setParameter('class', "dateTimePicker");
	}
	
	public function getTimestamp() {
		return $this->timestamp;
	}
	
	public function setInputName($name) {
		$this->inputname = $name;
		$this->setParameter('id', "field_$name");
	}
	
	public function getChildren() {
		
		#Years first
		$years = new HTMLSelect($this->inputname . '[year]', date('Y', $this->timestamp));
		$current_year = date('Y', time());
		for ($i = 1970; $i < $current_year + 3; $i++) {
			$years->addChild(new HTMLOption($i, $i));
		}
		
		#Months
		$months = new HTMLSelect($this->inputname . '[month]', date('m', $this->timestamp));
		for ($i = 0; $i < 12; $i++) {
			$months->addChild(new HTMLOption($i+1, self::$months[$i]));
		}
		
		#Days
		$days = new HTMLSelect($this->inputname . '[day]', date('d', $this->timestamp));
		for ($i = 0; $i < 31; $i++) {
			$days->addChild(new HTMLOption($i+1, $i+1));
		}
		
		#Hours and minutes
		$hours   = new HTMLInput('text', $this->inputname . '[hours]',   date('H', $this->timestamp));
		$minutes = new HTMLInput('text', $this->inputname . '[minutes]', date('i', $this->timestamp));
		
		return Array($years, $months, $days, $hours, $minutes);
	}
	
}
