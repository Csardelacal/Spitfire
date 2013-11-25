<?php namespace spitfire\validation;

/**
 * Represents an error detected while validating an element. This class contains 
 * information to aid a user of the system to solve the errors encountered and 
 * generating a valid input that is useful.
 * 
 * @author César de la Cal <cesar@magic3w.com>
 * @last-revision 2013-11-12
 */
class ValidationError
{
	/**
	 * The message given to the user to help fix the error. This is simply a message
	 * indicating what the error has been.
	 * @example . "Invalid email entered"
	 * @var string
	 */
	private $message;
	
	/**
	 * The additional information the system has available to help the user solve
	 * the error in case they could need it.
	 * @example . "An email must contain alphanumeric characters and @ and ."
	 * @var string 
	 */
	private $extendedMessage;
	
	/**
	 * A reference to the element generating the error. This allows assigning the 
	 * data and writing it next to the correct fields in case this behavior is 
	 * wanted.
	 * @var mixed
	 */
	private $src;
	
	/**
	 * A list of errors that are part of this error. This is useful when a field
	 * requires several validations like passwords or usernames which may 
	 * generate several errors at once.
	 * @var \spitfire\validation\ValidationError[]
	 */
	private $subErrors;
	
	/**
	 * Creates a new validation error. This represents an error during validation
	 * and holds information that should be helpful to resolve the source of it
	 * without the need of support. All the parameters except the error message 
	 * are optional.
	 * 
	 * @param string $message
	 * @param string $extendedMessage
	 * @param mixed $src
	 */
	public function __construct($message, $extendedMessage = '', &$src = null) {
		$this->message = $message;
		$this->extendedMessage = $extendedMessage;
		$this->src = &$src;
	}
	
	/**
	 * Gets the error message that explains the source of this in a user friendly 
	 * fashion. This message is meant to be delivered tot he end user of the 
	 * system.
	 * 
	 * @return string
	 */
	public function getMessage() {
		return $this->message;
	}
	
	/**
	 * Provides additional information about the error the data the user typed in
	 * has generated. This is an extended version of the message and should also
	 * provide useful help on solving the kind of error.
	 * 
	 * @return string
	 */
	public function getExtendedMessage() {
		return $this->extendedMessage;
	}
	
	/**
	 * Returns a reference to the element that generated the error. This allows to
	 * check where the error originated and print error data next to the data input
	 * we're using to read the data.
	 * 
	 * @return mixed
	 */
	public function &getSrc() {
		return $this->src;
	}
	
	/**
	 * Sets the message the user receives as reason why the data was rejected. This
	 * information is meant to provide him with means to fix the issue.
	 * 
	 * @param string $message
	 */
	public function setMessage($message) {
		$this->message = $message;
	}
	
	/**
	 * This message holds additional information to help the user fixing the data 
	 * they provided to a form or whatever they used to enter data.
	 * 
	 * @param string $extendedMessage
	 */
	public function setExtendedMessage($extendedMessage) {
		$this->extendedMessage = $extendedMessage;
	}
	
	/**
	 * Holds a reference to the source of the error. This will aid printing the 
	 * error data next to the input where it generated, making it easier for the
	 * users to spot where they entered invalid data.
	 * 
	 * @param mixed $src
	 */
	public function setSrc(&$src) {
		$this->src = &$src;
	}
	
	/**
	 * Returns a list of suberrors that this error may have generated. This allows
	 * you to specify several error information data in a single form control.
	 * 
	 * @return \spitfire\validation\ValidationError[]
	 */
	public function getSubErrors() {
		return $this->subErrors;
	}
	
	/**
	 * Adds a suberror to the object.
	 * 
	 * @param \spitfire\validation\ValidationError $subError
	 * @return \spitfire\validation\ValidationError
	 */
	public function putSubError($subError) {
		$this->subErrors[] = $subError;
		return $this;
	}
	
	/**
	 * Defines a list of suberrors to this one to make this error 'composite'. It 
	 * will provide a list of errors that all happened as part of this one.
	 * 
	 * @param \spitfire\validation\ValidationError[] $subErrors
	 * @return \spitfire\validation\ValidationError
	 */
	public function setSubErrors($subErrors) {
		$this->subErrors = $subErrors;
		return $this;
	}
	
	public function __toString() {
		$_return = '<li>';
		$_return.= $this->message;
		$_return.= ($this->extendedMessage)? $this->extendedMessage : '';
		$_return.= ($this->subErrors)? '<ul>' . implode('', $this->subErrors) . '</ul>' : '';
		$_return.= '</li>';
		return $_return;
	}
}