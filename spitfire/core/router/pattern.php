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
	 * The name of the variable that is being assigned the content of the result
	 * of this pattern. When the name is set the test will return an exception
	 * or an array like (name => value).
	 * 
	 * @var string
	 */
	private $name;
	
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
	
	/**
	 * Creates the pattern from the base string that comes with the URL. It will
	 * retrieve information whether the pattern was optional or not, what type it
	 * was and if it is a basic pattern.
	 * 
	 * @param string $pattern
	 */
	public function __construct($pattern) {
		$this->extractType($this->extractOptional($pattern));
	}
	
	/**
	 * This function will check if a pattern is optional. This means that it will
	 * return a valid result when it receives an empty value.
	 * 
	 * Please note that if it receieves a value and it's not valid it will return
	 * an error.
	 * 
	 * @param string $pattern
	 * @return string The rest of the pattern
	 */
	protected function extractOptional($pattern) {
		
		if (substr($pattern, -1) === '?') {
			$this->optional = true;
			$pattern        = substr($pattern, 0, -1);
		}
		
		return $pattern;
	}
	
	/**
	 * Assigns the type variable and/or pattern that is to be matched by reading
	 * a URL string that can be passed to the router as string. Even though the 
	 * router's matching mechanism is really basic it should be suffcient.
	 * 
	 * @param string $pattern
	 */
	protected function extractType($pattern) {
		
		switch ( substr($pattern, 0, 1) ) {
			case ':':
				$this->type     = self::WILDCARD_STRING;
				$this->name     = substr($pattern, 1);
				$this->pattern  = null;
				break;
			case '#':
				$this->type     = self::WILDCARD_NUMERIC;
				$this->name     = substr($pattern, 1);
				$this->pattern  = null;
				break;
			default:
				$this->type     = self::WILDCARD_NONE;
				$this->name     = null;
				$this->pattern  = explode('|', $pattern);
				break;
		}
	}
	
	/**
	 * Tests whether a string staisfies the pattern being optional. This will always
	 * return false if the pattern is not optional or the string being tested is 
	 * not empty.
	 * 
	 * @param type $str
	 * @return type
	 */
	public function testOptional($str) {
		if ($this->optional && empty($str)) {
			if ($this->type === self::WILDCARD_NONE) {return Array();}
			else { return Array($this->name => null);}
		}
		
		return false;
	}
	
	/**
	 * Tests whether the string matches the current pattern and returns an array
	 * in case it does. Otherwise it throws an exception.
	 * 
	 * @todo This could probably be better if split into several functions.
	 * @param string $str
	 * @return string[]
	 * @throws RouteMismatchException
	 */
	public function testString($str) {
		switch ($this->type) {
			case self::WILDCARD_NUMERIC:
				if (((int)$str) !== 0 && $this->testPattern($str)) { return Array($this->name => $str); } break;
			case self::WILDCARD_STRING:
				if (is_string($str)   && $this->testPattern($str)) { return Array($this->name => filter_var($str, FILTER_SANITIZE_STRING)); } break;
			default:
				if ($this->testPattern($str)) { return $this->name? Array($this->name => $str) : Array(); } break;
		}
		
		#If the pattern wasn't matched throw us out of it
		throw new RouteMismatchException();
	}
	
	/**
	 * Tests whether a string satisfies this pattern and returns the value it read
	 * out or throws an exception indicating that the route wasn't matched.
	 * 
	 * @throws RouteMismatchException
	 * @param string $str
	 * @return string
	 */
	public function test($str) {
		$r = $this->testOptional($str);
		return ($r !== false)? $r : $this->testString($str);
	}
	
	/**
	 * Returns whether the pattern was matched. This depends on the type of pattern.
	 * * If it's a Closure the result of the closure will determined if it's ok
	 * * Array's will be searched for a match
	 * * Strings will be splitted by pipe characters (|) and then searched
	 * 
	 * @param type $value
	 * @return boolean
	 */
	public function testPattern($value) {
		#If the pattern is null then it is always valid
		if ($this->pattern === null) {
			return true;
		#If the pattern is an array then we search it for the value
		} elseif (is_array($this->pattern)) {
			return in_array($value, $this->pattern);
		#If the pattern has been passed as a closure it will execute it and return the value
		} elseif ($this->pattern instanceof \Closure) {
			return $this->pattern($value);
		#Otherwise
		} else {
			return in_array($value, explode('|', $this->pattern));
		}
	}
	
	/**
	 * Defines the pattern to be used to test. This can be either a string, closure
	 * or an array.
	 * 
	 * @param type $pattern
	 */
	public function setPattern($pattern) {
		$this->pattern = $pattern;
	}
}