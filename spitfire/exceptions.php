<?php

/**
 * Functions for error detection and handling.
 * This set of functions help handling errors caused by the lower levels of
 * nLive.
 * @author César de la Cal Bretschneider <cesar@magic3w.com>
 * @package nLive
 */

/*
 * This classes look really harmless. Basically they are normal
 * exceptions that trigger different catch blocks.
 * This is a great way to handle different error types (for example
 * a database error and a user not found error)
 */

/**
 * Private Exceptions
 * If this kind of exception (or any inherited from it) reaches the exception_handler
 * it will hide the message from the user and try to log information about the
 * error that caused this.
 * 
 * @package nLive
 * @author César de la Cal Bretschneider <cesar@magic3w.com>
 */
class privateException extends Exception {}

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
 * @package nLive
 * @author César de la Cal Bretschneider <cesar@magic3w.com>
 */
class publicException  extends Exception {}
class fileNotFoundException  extends privateException {}
class filePermissionsException  extends privateException {}

/**
 * Output error page to browser
 * 
 * This function retrieves the error page for discrete failure when
 * the system encounters an error. If it doesn't find any file
 * to use it will throw an exception informing that no error
 * page could be found.
 * 
 * @param mixed $code
 * @param string $message
 */
function get_error_page($code, $message, $moreInfo) {
	$error_page = 'bin/error_pages/'.$code.'.php';
	if (file_exists($error_page)) {
		include $error_page;
		die();
	} elseif (file_exists($error_page = 'bin/error_pages/default.php')) {
		include $error_page;
		die();
	} else {
		echo 'Error page not found. 
			  To avoid this message please go to bin/error_pages and create '.$code.'.php with the data about the error you want.';
		throw new fileNotFoundException('File not found: '.$error_page, 500);
	}
}

/**
 * Silent exception handler.
 * 
 * Whenever an uncaught exception reaches the server it will use this
 * function for "discrete" failure. The function retrieves (depending
 * on the error) a error page and logs the error so it can be  
 * analyzed later.
 * In case there is a failover, and the function fails or cannot
 * find a file to display the error page it will try to handle the error
 * by causing a "white screen of death" to the user adding error information
 * to a HTML comment block. As it is the only failsafe way of communication
 * when there is a DB Error or permission error on the log files.
 * 
 * @param Exception $e
 */

class _SF_ExceptionHandler {

	private $msgs = Array();

	public function __construct() {
		set_exception_handler( Array($this, 'exceptionHandle'));
		set_error_handler    ( Array($this, 'errorHandle'), error_reporting() );
	}

	public function exceptionHandle (Exception $e) {
		try {
			ob_get_clean(); //The content generated till now is not valid. DESTROY. DESTROY!

			if ( is_a($e, 'publicException') ) {
				get_error_page($e->getCode(), $e->getMessage());
			} else {
				error_log($e->getMessage());
				if (environment::get('debugging_mode')) get_error_page( 500, $e->getMessage() );
				else                                    get_error_page(500, 'Server error');
			}

		} catch (Exception $e) { //Whatever happens, it won't leave this function
			echo '<!--'.$e->getMessage().'-->';
			die();
		}
	}

	public function errorHandle ($errno, $errstr, $errfile, $errline, $scope) {
		while(ob_get_clean());
		get_error_page(500, "Error $errno: $errstr in $errfile [$errline]", print_r($scope, 1) );
		return false;
	}

	public function msg ($msg) {
		$this->msgs[] = $msg;
	}

	public function getMessages () {
		return $this->msgs;
	}
}

/**
 * Electric Fence.
 * 
 * This function is inspired by PHP Unit, it is used to detect correctness of 
 * code. The idea behind it is easy if everything is working as expected nothing 
 * happens, elseway it will throw an error.
 * 
 * This allows us to check if impossible situations happen and stop the execution
 * early. For example, reading data from DB by Key:
 * <code>assertion(mysql_num_rows($result) == 1)</code>
 * This way we could detect an SQL injection attempt and log the attack.
 */

function assertion($condition, $msg = "Assertion failed") {
	if (!$condition)
		throw new PrivateException($msg);
}

/**
 * Send a message to linux terminal
 * 
 * Allows us to record errors to linux terminal and inform everybody connected
 * via terminal to the server about errors in real-time.
 * 
 * @param mixed $msg Message to send to terminal
 * @return True if the message has been sent, false on error.
 */
function log_msg($msg) {
	if (is_array($msg) || is_object($msg)) $msg = print_r($msg, true);
	return !(system("echo '$msg' | wall"));
}