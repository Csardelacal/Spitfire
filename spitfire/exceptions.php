<?php


/*
 * This classes look really harmless. Basically they are normal
 * exceptions that trigger different catch blocks.
 * This is a great way to handle different error types (for example
 * a database error and a user not found error)
 */

class filePermissionsException  extends \spitfire\exceptions\PrivateException {}

/**
 * Public Exceptions
 * If this kind of exception (or any inherited from it) reaches the exception_handler
 * it will give the user information about the cause of the error. This is useful
 * to handle HTTP Error Codes.
 * For example: 
 * <code>throw new publicException('User not found', 404);</code>
 * Will cause the browser to display an error page with the message 'User not found'
 * and keep everything else hidden.
 * 
 * @author César de la Cal Bretschneider <cesar@magic3w.com>
 */
class publicException  extends Exception {}
