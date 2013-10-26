<?php

namespace spitfire\locales;

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
		
		$classname = str_replace('-', '\\', $this->localecode) . 'Locale';
		if (class_exists($classname)) return true;
		
		$classname = substr($this->localecode, 0, 2) . 'Locale';
		if (class_exists($classname)) return true;
		
		$classname = 'spitfire\system\\' . $this->langcode . 'Locale';
		if (class_exists($classname)) return true;
		
		$classname = 'spitfire\system\\' . str_replace('-', '\\', $this->localecode) . 'Locale';
		if (class_exists($classname)) return true;
	}
	
	public function getLocaleClass($context) {
		try {
			return $context->app->getLocale($this->langcode);
		}
		catch(privateException $e) {
			try {
				return $context->app->getLocale(substr($this->localecode, 0, 2));
			}
			catch(privateException $e) {
				if (class_exists($t = 'system\\' . $this->langcode . 'Locale')) { return new $t();}
				if (class_exists($t = 'system\\' . substr($this->localecode, 0, 2) . 'Locale')) { return new $t();}
				return new system\enLocale();
			}
		}
	}
}
