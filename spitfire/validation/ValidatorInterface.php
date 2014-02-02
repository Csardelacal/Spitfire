<?php namespace spitfire\validation;

/**
 * The validator interface is the interface that every element should implement
 * to indicate that it's content can be verified for correctness. Every validator
 * should allow to make itself more restrictive during runtime to ensure that
 * higher security restrictions can be applied under some conditions.
 * 
 * @author César de la Cal <cesar@magic3w.com>
 */
interface ValidatorInterface 
{
	/**
	 * Checks whether the content is correct and throws a validation exception in
	 * case it is not. This makes this method easily overridable to be able to 
	 * generate more diverse error messages when using the method.
	 * 
	 * For example, if your application applies a special behavior when data has 
	 * been submitted rather than unsubmitted, your application can throw and catch
	 * each type of error making the code easier to read.
	 */
	function validate();
	
	/**
	 * Checks the content. Returns true or false depending on the data being
	 * received being correct or not. The test should cascade automatically into
	 * child values.
	 * 
	 * @return boolean True if the data is valid
	 */
	function isOk();
	
	/**
	 * Returns the list of errors generated when validating the content. For this
	 * method it's up to the method's implementation to decide whether the method
	 * should be recursive and cascade into child values collecting all errors or
	 * just the ones originated by the content itself.
	 * 
	 * @return ValidationError[] List of errors
	 */
	function getMessages();
	
	/**
	 * Allows the application using this to apply additional restrictions to the 
	 * base ones (optional) to restrict the possibilities of the data being valid
	 * further.
	 */
	function addRule(ValidationRule$rule);
	
}
