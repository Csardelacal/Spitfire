<?php namespace spitfire\exceptions;

/**
 * 
 * A private exception represents any kind of error that is not meant to be 
 * broadcasted to the end user. Usually this includes system / file / security
 * errors as opposed to page_not_found or similar errors.
 * 
 * If this kind of exception (or any inherited from it) reaches the exception_handler
 * it will hide the message from the user and try to log information about the
 * error that caused this.
 * 
 * @author César de la Cal Bretschneider <cesar@magic3w.com>
 */
class PrivateException extends \Exception
{
	
}
