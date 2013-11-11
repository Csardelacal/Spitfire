<?php namespace spitfire\validation;

/**
 * A validator is a tool that can be used in order to verify that data is correct.
 * This data can basically be everything, although validation is usually aimed at
 * strings, which are the most common daatype when interacting with a user.
 * 
 * It uses a set of rules, that can return either a validationError or a boolean 
 * false if no errors where encountered. If you want your validation rules to 
 * return several errors you will have to generate a validation arror that holds
 * several suberrors.
 * 
 * @author César de la Cal <cesar@magic3w.com>
 */
class Validator
{
	/**
	 * The set of rules used to validate data that is tested with these.
	 * @var ValidationRule[]
	 */
	private $rules;
	
	/**
	 * Adds a rule to this validator. This way it can test the correctness of 
	 * data varying on user's preferences.
	 * 
	 * @param \spitfire\validation\ValidationRule $rule
	 * @return \spitfire\validation\Validator
	 */
	public function addRule(ValidationRule$rule) {
		$this->rules[] = $rule;
		return $this;
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
	 * Tests an element against the validation rules inside this validator. If the
	 * element is a validatable object it will return the status of validity of 
	 * the object.
	 * 
	 * For easier to read and cleaner syntax this method throws an exception containing
	 * data about all the errors generated while testing so you can assign them to
	 * tested values.p
	 * 
	 * @param \spitfire\validation\Validatable|mixed $value
	 * @param mixed $src
	 * @return \spitfire\validation\ValidationResult
	 * @throws \ValidationException If the validation is not correct.
	 */
	public function test($value, &$src = null) {
		if ($value instanceof Validatable) {return $value->validate();}
		
		$errors = Array();
		foreach ($this->rules as $rule) {
			$errors[] = $this->testRule($rule, $value, $src);
		}
		
		$result = new ValidationResult(array_filter($errors));
		if ($result->success()) { return $result; }
		else { throw self::makeException()->setResult($result);};
	}
	
	/**
	 * Tests a value against a single rule.
	 * 
	 * @param \spitfire\validation\ValidationRule $rule
	 * @param mixed $value
	 * @param type $src
	 * @return \spitfire\validation\ValidationError
	 * @throws \privateException If the rule returns unexpected data.
	 */
	protected function testRule(ValidationRule$rule, $value, &$src) {
		$result = $rule->test($value, $src);

		if ($result !== false || !$result instanceof ValidationError) {
			throw new \privateException('Invalid result type, expected ValidationResult');
		}
		
		return $result;
	}
	
	/**
	 * Constructs a validation exception. This streamlines the way code is written
	 * as it will require to write it with catch's instead of big if nests that
	 * are not required in most cases.
	 * 
	 * @return \ValidationException
	 */
	protected function makeException() {
		return new \ValidationException('Validation failed', 0);
	}
}