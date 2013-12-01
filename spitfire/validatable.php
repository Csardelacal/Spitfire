<?php

abstract class Validatable
{
	
	private $errors = Array();
	
	public function validate ($data = null) {
		
		$valid = true;
		$this->errors = Array();
		
		foreach ($data as $field => $value) {
			#Determine the function name this should have
			$fnname = 'validate' . ucfirst($field);
			#Try to validate
			if (method_exists($this, $fnname)) {
				$callback = Array($this, $fnname);
				$valid = $valid && call_user_func_array($callback, Array($value) );
			}
			#If there is no way to validate guess it's ok
		}
		
		
		return $valid;
	}
	
	public function validationError($msg) {
		$this->errors[] = $msg;
	}
	
	public function getErrors() {
		return $this->errors;
	}
	
}