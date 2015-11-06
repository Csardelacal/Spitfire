<?php namespace spitfire\io\stp;

use Exception;

/**
 * This class provides Spitfire with tools to print a pretty and informative
 * Stack trace of the error it received.
 */
abstract class StackTracePrinter
{
	
	/**
	 * The exception being printed. It provides the necessary information for the 
	 * Printer to display it to a user in a way that becomes useful to them.
	 *
	 * @var \Exception
	 */
	private $exception;
	
	/*
	 * The system distinguishes three types of lines: error, warn and normal lines.
	 * 
	 * The error line will be the one that caused the exception to be thrown, the 
	 * warnings will be the lines that did not directly invoke the function throwing
	 * the exception but that are part of the stack trace.
	 * 
	 * All other lines are normal. Obviously.
	 */
	const LINE_TYPE_ERROR  = 'error';
	const LINE_TYPE_WARN   = 'warn';
	const LINE_TYPE_NORMAL = 'normal';
	
	/**
	 * Creates the printer. This allows the application to return a "nice" and 
	 * helpful error to the developer to avoid him needing to revisit the entire
	 * application code and having an excerpt on screen the moment it fails.
	 * 
	 * @param Exception $e
	 */
	public function __construct(Exception$e) {
		$this->exception = $e;
	}
	
	/**
	 * Walks over the components of the stack trace being printed and stringifies
	 * them. Please note that, in order to do so, it relies on the overriden and
	 * implemented methods from the child class.
	 * 
	 * @todo Handle the behavior when the stack trace is unpopulated.
	 * @return string
	 */
	public function iterateTrace() {
		#Get the trace and init the string we're gonna be using to collect results
		$trace = $this->exception->getTrace();
		$_ret  = '';
		
		#Loop over the trace and collect the results into _ret
		foreach($trace as $entry) {
			$_ret.= $this->stringifyEntry($entry);
		}
		
		#Return _ret
		return $_ret;
	}
	
	public function stringifyEntry($entry) {
		$_ret = '';
		$_ret.= $this->wrapMethodSignature($this->printMethodSignature(isset($entry['class'])? $entry['class'] . $entry['type'] . $entry['function'] : $entry['function'], $entry['args']));
		
		if (!empty($entry['file'])) {
			$_ret.= $this->makeExcerpt($entry['file'], $entry['line']);
		} else {
			$_ret.= $this->wrapExcerpt('[Internal function]', null);
		}
		
		return $_ret;
	}
	
	public function stringifyArgs($args) {
		$_ret = Array();
		
		foreach ($args as $arg) {
			if     (is_object($arg)) { $_ret[]= get_class($arg); }
			elseif (is_array($arg))  { $_ret[]= sprintf('Array(%s)', count($arg)); }
			elseif (is_int($arg))    { $_ret[]= sprintf('Integer(%s)', $arg); }
			elseif (is_double($arg)) { $_ret[]= sprintf('Double(%s)', $arg); }
			elseif (is_string($arg)) { $_ret[]= sprintf('String(%s)', strlen($arg) > 30? strlen($arg) : $arg); }
		}
		
		return implode(', ', $_ret);
	}
	
	public function makeExcerpt($file, $line) {
		$line    = $line - 1; //Compensate for the fact that the file is an array now
		$content = file($file);
		$_ret    = Array();
		
		for ($i = $line - 5; $i < $line; $i++) {
			if ($i > 0) {$_ret[] = $this->printLine($content[$i]); }
		}
		
		#The affected line displays the error source
		$_ret[] = $this->printLine($content[$line], self::LINE_TYPE_ERROR);
		
		for ($i = $line + 1; $i < $line + 6; $i++) {
			if (isset($content[$i])) {$_ret[] = $this->printLine($content[$i]); }
		}
		
		return $this->wrapExcerpt(implode('', $_ret), $line + 1);
	}
	
	public function __toString() {
		if (php_sapi_name() === 'cli') { return $this->exception->getTraceAsString(); }
		
		return $this->wrapStackTrace($this->iterateTrace());
	}
	
	#Abstract methods which are in charge of styling and interactivity
	abstract public function wrapStackTrace($html);
	abstract public function wrapMethodSignature($html);
	abstract public function printMethodSignature($function, $args);
	abstract public function printLine($line, $type = StackTracePrinter::LINE_TYPE_NORMAL);
	abstract public function wrapExcerpt($html, $startLine);
	abstract public function makeCSS();
	
}