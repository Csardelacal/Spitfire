<?php namespace spitfire\router;

class Pattern
{
	
	const WILDCARD_NONE    = 0;
	const WILDCARD_STRING  = 1;
	const WILDCARD_NUMERIC = 2;
	
	private $type;
	private $pattern;
	
	public function __construct($pattern) {
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
				$this->pattern  = $pattern;
				break;
		}
	}
	
	public function test($str) {
		switch ($this->type) {
			case self::WILDCARD_NUMERIC:
				if (((int)$str) !== 0) { return [$this->pattern => $str]; }
				break;
			case self::WILDCARD_STRING:
				if (is_string($str)) { return [$this->pattern => filter_var($str, FILTER_SANITIZE_STRING)]; }
				break;
			default:
				if ($str === $this->pattern) { return []; }
				break;
		}
		
		//If the pattern wasn't matched throw us out of it
		throw new RouteMismatchException();
	}
	
}