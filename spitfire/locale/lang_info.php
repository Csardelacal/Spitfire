<?php

namespace spitfire\locales;

use spitfire\Request;
use \privateException;

class langInfo
{
	private $quality = 1.0;
	private $langcode = '';
	private $localecode = '';
	
	public function __construct($str) {
		$data = explode(';', $str);
		
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
		try {
			return Request::get()->getIntent()->getApp()->getLocale($this->langcode);
		}
		catch(privateException $e) {
			try {
				return Request::get()->getIntent()->getApp()->getLocale(substr($this->localecode, 0, 2));
			}
			catch(privateException $e) {
				return new \enLocale();
			}
		}
	}
}
