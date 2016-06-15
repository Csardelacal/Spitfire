<?php namespace spitfire\validation;

use spitfire\exceptions\PrivateException;

/**
 * A validator is a tool that can be used in order to verify that data is correct.
 * This data can basically be everything, although validation is usually aimed at
 * strings, which are the most common daatype when interacting with a user.
 * 
 * It uses a set of rules, that can return either a validationError or a boolean 
 * false if no errors where encountered. 
 * 
 * @author César de la Cal <cesar@magic3w.com>
 */
class Validator implements ValidatorInterface
{
	/**
	 * The set of rules used to validate data that is tested with these.
	 * @var ValidationRule[]
	 */
	private $rules = Array();
	
	/**
	 * Holds the array of messages that generated when validating the content.
	 * This can be used to inform the user verbosely about the kind of error that
	 * generated when testing the content.
	 * @var ValidationError[]
	 */
	private $messages;
	
	/**
	 * The value this element is testing for validity. When the data is set the 
	 * validator will re-run all the tests on the next call to one of the methods
	 * that provides validation status.
	 * @var mixed
	 */
	private $value;
	
	/**
	 * Adds a rule to this validator. This way it can test the correctness of 
	 * data varying on user's preferences.
	 * 
	 * @param \spitfire\validation\ValidationRule $rule
	 * @return \spitfire\validation\Validator
	 */
	public function addRule(ValidationRule$rule) {
		$this->rules[] = $rule;
		$message = $this->testRule($rule, $this->value);
		if ($message) { $this->messages[] = $message; }
		return $this;
	}
	
	/**
	 * Defines the value to be tested. The validator will then use the existing 
	 * functions to inform you about the status fo the validation of this value.
	 * 
	 * @param mixed $value
	 * @return \spitfire\validation\Validator
	 */
	public function setValue($value) {
		$this->value    = $value;
		$this->iterateRules();
		return $this;
	}
	
	/**
	 * Returns the value this validator is containing and that is being tested 
	 * against the rules inside it.
	 * 
	 * @return mixed
	 */
	public function getValue() {
		return $this->value;
	}
	
	/**
	 * Returns the list of messages that the validator has generated when testing 
	 * it's content.
	 * 
	 * @return type Description
	 */
	public function getMessages() {
		return $this->messages;
	}
	
	/**
	 * Returns true if the data held by the validator is acceptable and passes all
	 * the tests being requested to consider the data valid.
	 * 
	 * @return boolean
	 */
	public function isOk() {
		return empty($this->messages);
	}
	
	/**
	 * Throws an exception in case the data is not valid. This is often the most 
	 * comfortable way to return errors as it will simply break layers that are not
	 * able to handle it and therefore making the application safer, forcing it to
	 * react to the error or fail.
	 * 
	 * @throws ValidationException
	 */
	public function validate() {
		if (!empty($this->messages)) {
			throw self::makeException($this->messages);
		}
	}
	
	/**
	 * Adds minimum length validation to this object. It will cause an error if
	 * the data tested is not long enough, this is meant for strings, and will 
	 * probably cause faulty behavior when testing agains other stuff.
	 * 
	 * @param int $length
	 * @param string $msg
	 * @param string $longmsg
	 * @return \spitfire\validation\Validator
	 */
	public function minLength($length, $msg, $longmsg = '') {
		$this->addRule(new MinLengthValidationRule($length, $msg, $longmsg));
		return $this;
	}
	
	
	/**
	 * Adds maximum length validation to this object. It will cause an error if
	 * the data tested is longer than expected, this is meant for strings, and will 
	 * probably cause faulty behavior when testing agains other stuff.
	 * 
	 * @param int $length
	 * @param string $msg
	 * @param string $longmsg
	 * @return \spitfire\validation\Validator
	 */
	public function maxLength($length, $msg, $longmsg = '') {
		$this->addRule(new MaxLengthValidationRule($length, $msg, $longmsg));
		return $this;
	}
	
	/**
	 * Validates a content as email. This evaluates the string value of what it 
	 * receives and may cause unexpected behavior.
	 * 
	 * @param string $msg
	 * @param string $longmsg
	 * @return \spitfire\validation\Validator
	 */
	public function asEmail($msg, $longmsg = '') {
		$this->addRule(new FilterValidationRule(FILTER_VALIDATE_EMAIL, $msg, $longmsg));
		return $this;
	}
	
	/**
	 * Validates a content as a URL. This evaluates the string value of what it 
	 * receives and may cause unexpected behavior.
	 * 
	 * @param string $msg
	 * @param string $longmsg
	 * @return \spitfire\validation\Validator
	 */
	public function asURL($msg, $longmsg = '') {
		$this->addRule(new FilterValidationRule(FILTER_VALIDATE_URL, $msg, $longmsg));
		return $this;
	}
	
	/**
	 * Loops throught the list of rules this validator has testing if all of them 
	 * are satisfied by the value.
	 * 
	 * @param mixed $value
	 */
	private function iterateRules() {
		$errors = Array();
		foreach ($this->rules as $rule) {
			$errors[] = $this->testRule($rule, $this->value);
		}
		$this->messages = array_filter($errors);
	}
	
	/**
	 * Tests a value against a single rule.
	 * 
	 * @param \spitfire\validation\ValidationRule $rule
	 * @param mixed $value
	 * @return \spitfire\validation\ValidationError
	 * @throws PrivateException If the rule returns unexpected data.
	 */
	protected function testRule(ValidationRule$rule, $value) {
		$result = $rule->test($value);
		
		#If the result was a string it means that the system returned a message
		if (is_string($result)) {
			$result = new ValidationError($result);
		}

		if ($result !== false && $result !== true && $result !== null && !$result instanceof ValidationError) {
			throw new PrivateException('Invalid result type, expected ValidationError');
		}
		
		return $result;
	}
	
	/**
	 * Constructs a validation exception. This streamlines the way code is written
	 * as it will require to write it with catch's instead of big if nests that
	 * are not required in most cases.
	 * 
	 * @param ValidationError[] $errors
	 * @return \ValidationException
	 */
	public static function makeException($errors) {
		$ex = new ValidationException('Validation failed', 0, $errors);
		return $ex;
	}

}