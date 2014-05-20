<?php namespace spitfire\core\router;

/**
 * The pattern class allows to test an URL fragment (the piece that originates 
 * when splitting the path by '/'). While the router doesn't allow to use complex
 * regular expressions it therefore increases the stability and security.
 * 
 * Users don't need to learn nor understand how a regular expression works in order
 * to use this more simple patterns. This allow a user to test a string and assign
 * it an ID the user can use to retrieve it.
 * 
 * @author César de la Cal <cesar@magic3w.com>
 */
class Pattern
{
	/**
	 * Indicates the pattern is not a wildcard but either a static value or a 
	 * series of values separated by '|'. This pattern will return an empty array
	 * in case of a succesful test.
	 */
	const WILDCARD_NONE    = 0;
	
	/**
	 * Indicates the pattern is testing for a string. In this case, every string 
	 * will be matched. This allows your application to use the pattern as name
	 * for the parameter.
	 * 
	 * The constructor will consider a string wildcard every parameter starting
	 * with a colon (:).
	 */
	const WILDCARD_STRING  = 1;
	
	/**
	 * If the string passed with the URL is a integer then it will be accepted as
	 * such and be parsed. Otherwise the router will receive a RouteMismatch Exception
	 * indicating that the string was not valid.
	 */
	const WILDCARD_NUMERIC = 2;
	
	/**
	 * The pattern type. This will define how the Pattern decides which content 
	 * is to be tested as valid. This can be any of the WILDCARD_ constants this 
	 * class defines.
	 * 
	 * @var int
	 */
	private $type;
	
	/**
	 * The pattern to be tested. In case the router is testing for a wildcard this
	 * will contain the name of the parameter to be return in case of a success.
	 *
	 * @var string
	 */
	private $pattern;
	
	/**
	 * Indicates whether the Pattern has to e satisfied or not. In case it is optional
	 * this variable may contain true (incase the default value was left empty)
	 * or the content that is to be assumed in case nothing was set.
	 *
	 * @var boolean|string
	 */
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
				if (is_string($str)) { return Array($this->pattern => filter_var($str, FILTER_SANITIZE_STRING)); }
				break;
			default:
				if (in_array($str, $this->pattern)) { return Array(); }
				break;
		}
		
		//If the pattern wasn't matched throw us out of it
		throw new RouteMismatchException();
	}
	
}