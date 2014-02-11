<?php namespace spitfire\router;

class Pattern
{
	
	const WILDCARD_NONE    = 0;
	const WILDCARD_STRING  = 1;
	const WILDCARD_NUMERIC = 2;
	
	private $type;
	private $pattern;
	private $optional = false;
	
	public function __construct($pattern) {
		
		if (substr($pattern, -1) === '?') {
			$this->optional = true;
			$pattern        = substr($pattern, 0, -1);
		}
		
		switch ( substr($pattern, 0, 1) ) {
			case ':':
				$this->type    = self::WILDCARD_STRING;
				$this->pattern = substr($pattern, 1);
				break;
			case '#':
				$this->type     = self::WILDCARD_NUMERIC;
				$this->pattern  = substr($pattern, 1);
				break;
			default:
				$this->type     = self::WILDCARD_NONE;
				$this->pattern  = explode('|', $pattern);
				break;
		}
	}
	
	public function test($str) {
		if ($this->optional && empty($str)) {
			if ($this->type === self::WILDCARD_NONE) {return Array();}
			else { return Array($this->pattern => null);}
		}
		
		switch ($this->type) {
			case self::WILDCARD_NUMERIC:
				if (((int)$str) !== 0) { return Array($this->pattern => $str); }
				break;
			case self::WILDCARD_STRING:
				if (is_string($str)) { return ARray($this->pattern => filter_var($str, FILTER_SANITIZE_STRING)); }
				break;
			default:
				if (in_array($str, $this->pattern)) { return Array(); }
				break;
		}
		
		//If the pattern wasn't matched throw us out of it
		throw new RouteMismatchException();
	}
	
}