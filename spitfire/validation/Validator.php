<?php namespace spitfire\validation;

class Validator
{
	
	private $rules;
	
	public function addRule(ValidationRule$rule) {
		$this->rules[] = $rule;
		return $this;
	}
	
	public function minLength($length, $msg, $longmsg = '') {
		$this->addRule(new MinLengthValidationRule($length, $msg, $longmsg));
		return $this;
	}
	
	public function maxLength($length, $msg, $longmsg = '') {
		$this->addRule(new MaxLengthValidationRule($length, $msg, $longmsg));
		return $this;
	}
	
	public function asEmail($msg, $longmsg = '') {
		$this->addRule(new FilterValidationRule(FILTER_VALIDATE_EMAIL, $msg, $longmsg));
		return $this;
	}
	
	public function asURL($msg, $longmsg = '') {
		$this->addRule(new FilterValidationRule(FILTER_VALIDATE_URL, $msg, $longmsg));
		return $this;
	}
	
	public function test($value, $src = null) {
		if ($value instanceof Validatable) {return $value->validate();}
		
		$errors = Array();
		foreach ($this->rules as $rule) {
			$result = $this->testRule($rule, $value, $src);
			if ($result !== false) {$errors[] = $result;}
		}
		return new ValidationResult(array_filter($errors));
	}
	
	protected function testRule($rule, $value, $src) {
		$result = $rule->test($value, $src);

		if (!$result instanceof ValidationResult) {
			throw new \privateException('Invalid result type, expected ValidationResult');
		}
		
		if ($result->success()) { return false;}
		else {return $result->getErrors();}
	}
}