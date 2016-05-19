<?php namespace spitfire\validation;

/**
 * Validates a value is not empty. This allows the system to make sure that a 
 * required field has not been field with useless data.
 * 
 * @author César de la Cal <cesar@magic3w.com>
 */
class RegexValidationRule implements ValidationRule
{
	
	/**
	 * The regular expression we wish to test the content of the validator against.
	 *
	 * @var string
	 */
	private $regex;
	
	/**
	 * A message the validation error generated by this object should carry to give
	 * the end user information about the reason his input was rejected.
	 * @var string
	 */
	private $message;
	
	/**
	 * Additional information given to the user in case the validation did not 
	 * succeed. This message can hold additional infos on how to solve the error.
	 * 
	 * @var string
	 */
	private $extendedMessage;
	
	public function __construct($regex, $message, $extendedMessage = '') {
		$this->regex = $regex;
		$this->message = $message;
		$this->extendedMessage = $extendedMessage;
	}
	
	/**
	 * Tests a value with this validation rule. Returns the errors detected for
	 * this element or boolean false on no errors.
	 * 
	 * @param mixed $value
	 * @param mixed $source
	 * @return \spitfire\validation\ValidationError|boolean
	 */
	public function test($value) {
		if (preg_match($this->regex, $value) !== 1) {
			return new ValidationError($this->message, $this->extendedMessage);
		}
		return false;
	}
	
}