<?php namespace spitfire\io\beans;

use spitfire\validation\Validator;
use spitfire\validation\ValidationRule;

class BasicField extends Field
{
	private $model_field;
	private $validator = null;
	
	public function getDefaultValue() {
		if ($this->getBean()->getRecord()) {
			return $this->getBean()->getRecord()->{$this->getModelField()};
		}
		else {
			return null;
		}
	}
	
	public function setModelField($name) {
		$this->model_field = $name;
		return $this;
	}
	
	public function getModelField() {
		return $this->getFieldName();
	}

	public function getPostTargetFor($name) {
		return null;
	}
	
	public function setPostData($post) {
		$this->getValidator()->setValue($post);
		parent::setPostData($post);
	}
	
	/**
	 * Returns the validator for this field. This enables the user to define rules
	 * that make this field testable to make sure content is valid.
	 * 
	 * @return Validator
	 */
	public function getValidator() {
		if ($this->validator === null) {
			$this->validator = new Validator();
		}
		return $this->validator;
	}
	
	/**
	 * Adds a validation rule to the field. When doing so we ensure the data will
	 * be correct when storing into the database AFTER we call validate or isOk.
	 * 
	 * @param ValidationRule $rule
	 * @return Validator
	 */
	public function addRule(ValidationRule $rule) {
		$this->getValidator()->addRule($rule);
		return $this;
	}

	public function getMessages() {
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') { return Array(); }
		return $this->getValidator()->getMessages();
	}

	public function isOk() {
		return $this->getValidator()->isOk();
	}

	public function validate() {
		return $this->getValidator()->validate();
	}

}