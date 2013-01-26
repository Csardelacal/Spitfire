<?php

namespace spitfire\exceptions;

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
function get_error_page($code, $message, $moreInfo = '') {
	$error_page = Spitfire::$cwd . '/bin/error_pages/'.$code.'.php';
	if (file_exists($error_page)) {
		include $error_page;
		die();
	} elseif (file_exists($error_page = Spitfire::$cwd . '/bin/error_pages/default.php')) {
		include $error_page;
		die();
	} else {
		echo 'Error page not found. 
			  To avoid this message please go to bin/error_pages and create '.$error_page .' with the data about the error you want.';
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

class ExceptionHandler {

	private $msgs = Array();

	public function __construct() {
		set_exception_handler( Array($this, 'exceptionHandle'));
		set_error_handler    ( Array($this, 'errorHandle'), error_reporting() );
		register_shutdown_function( Array($this, 'shutdownHook'));
	}

	public function exceptionHandle (Exception $e) {
		try {
			while(ob_get_clean()); //The content generated till now is not valid. DESTROY. DESTROY!

			if ( is_a($e, 'publicException') ) {
				get_error_page($e->getCode(), $e->getMessage() );
			} else { 
				error_log($e->getMessage());
				$trace = 'Trace: ' . print_r( $e->getTrace(), 1);
				if (environment::get('debugging_mode')) get_error_page(500, $e->getMessage(), $trace );
				else                                    get_error_page(500, 'Server error');
			}

		} catch (Exception $e) { //Whatever happens, it won't leave this function
			echo '<!--'.$e->getMessage().'-->';
			die();
		}
	}

	public function errorHandle ($errno, $errstr, $errfile, $errline, $scope) {
		if (!error_reporting()) return false;
		
		switch ($errno) {
			case E_ERROR:
			case E_CORE_ERROR:
			case E_COMPILE_ERROR:
			case E_USER_ERROR:
				while(ob_get_clean());
				echo getcwd();
				get_error_page(500, "Error $errno: $errstr in $errfile [$errline]", print_r($scope, 1) );
				return false;
				break;
			case E_DEPRECATED:
				echo "Deprecated function is being used.";
				return false;
				break;
			default:
				return false;
				break;
		}
	}
	
	public function shutdownHook () {
		$last_error = error_get_last();
		while(ob_get_clean()); 
		
		switch($last_error['type']){
			case E_ERROR:
			case E_CORE_ERROR:
			case E_COMPILE_ERROR:
			case E_USER_ERROR:
				get_error_page(500, $last_error['message'] . "@$last_error[file] [$last_error[line]]");
		}
	}

	public function msg ($msg) {
		$this->msgs[] = $msg;
	}

	public function getMessages () {
		return $this->msgs;
	}
}
