<?php

namespace spitfire\locales;

class langInfo
{
	private $quality = 1.0;
	private $langcode = '';
	private $localecode = '';
	
	public function __construct($str) {
		$data = explode(',', $str);
		
		foreach($data as $e) {
			$e = trim($e);
			if (strlen($e) == 2) $this->langcode = $e;
			if (strlen($e) == 5) $this->localecode = $e;
			else $this->quality = (float)substr($e, 0, 3);
		}
	}
	
	public function isUnderstood() {
		$classname = $this->langcode . 'Locale';
		if (class_exists($classname)) return true;
		
		$classname = substr($this->localecode, 0, 2) . 'Locale';
		if (class_exists($classname)) return true;
	}
	
	public function getLocaleClass() {
		$classname = $this->langcode . 'Locale';
		if (!empty($this->langcode) && class_exists($classname)) return new $classname();
		
		$classname = substr($this->localecode, 0, 2) . 'Locale';
		if (!empty($this->localecode) && class_exists($classname)) return new $classname();
		
		else return new \enLocale();
	}
}
