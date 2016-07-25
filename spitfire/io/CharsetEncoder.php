<?php namespace spitfire\io;

/**
 * The charset encoder class allows an application to convert strings between
 * two character sets and therefore acknowledging the needs of an external API,
 * browser or database.
 * 
 * The inner and outer encoding names are used to ease the naming and understanding
 * of when the application is converting from one encoding to another and 
 * viceversa.
 * 
 * Since it's common to use the encoders to encode data that may not be a string
 * because the target is sensible to null values and integers, we return numbers
 * and nulls unchanged.
 */
class CharsetEncoder
{
	
	private $inner;
	private $outer;
	
	/**
	 * A character set encoder allows your application to quickly translate strings
	 * from one encoding to another. To do so, all you need is to define an input 
	 * and output encoding (inner and outer) that will be used to convert between.
	 * 
	 * @param string $outer Name of the outer encoding
	 * @param string $inner Name of the inner encoding
	 */
	public function __construct($outer, $inner) {
		$this->inner = $inner;
		$this->outer = $outer;
	}
	
	/**
	 * Converts data from the outer encoding to the inner encoding that you defined.
	 * 
	 * @param String $str The string encoded with the database's encoding
	 * @return String The string encoded with Spitfire's encoding
	 */
	public function encode($str) {
		if ($str === null)    { return null; }
		if (is_numeric($str)) { return $str; }
		return mb_convert_encoding($str, $this->inner, $this->outer);
	}
	
	
	/**
	 * Converts data from the inner encoding to the outer encoding that you defined.
	 * 
	 * @param String $str The string encoded with Spitfire's encoding
	 * @return String The string encoded with the database's encoding
	 */
	public function decode($str) {
		if ($str === null)    { return null; }
		if (is_numeric($str)) { return $str; }
		return mb_convert_encoding($str, $this->outer, $this->inner);
	}
	
	/**
	 * The encoding you defined as inner. This is the encoding that encode will 
	 * convert <strong>to</strong> and decode will convert <strong>from</strong>
	 * 
	 * @return string
	 */
	public function getInnerEncoding() {
		return $this->inner;
	}

}
