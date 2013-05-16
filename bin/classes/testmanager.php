<?php

/**
 * This class collects the results of tests when they are finished and allows to
 * print them in a accurate manner.
 */
class TestManager
{
	public static $instance = null;
	
	private $performed = 0;
	private $failed    = 0;
	private $errors    = Array();
	
	private $skip      = Array(
	    '__construct',
	    '__destruct',
	    'getParameters',
	    'setup',
	    'cleanup'
	);
	
	protected function __construct() {
		;
	}
	
	public function assertEquals($a, $b, $enforce_type = false) {
		if ($enforce_type) $result = $a === $b;
		else $result = $a == $b;
		
		$this->performed++;
		if (is_object($a)) $a = get_class($a);
		if (is_object($b)) $a = get_class($b);
		
		if (!$result) $this->error("'$a' does not equal '$b'");
	}
	
	public function error($msg) {
		$this->errors[] = $msg;
		$this->failed++;
	}
	
	public function test(Test$test) {
		$methods = get_class_methods($test);
		
		if (is_callable(Array($test, 'setup'))) {
			call_user_func_array(Array($test, 'setup'), $test->getParameters());
		}
		
		foreach($methods as $method) {
			if (in_array($method, $this->skip)) continue;
			call_user_func_array(Array($test, $method), $test->getParameters());
			$this->performed++;
		}
	}
	
	public function getResult() {
		return Array(
		    'performed' => $this->performed,
		    'failed'    => $this->failed,
		    'errors'    => $this->errors
		);
	}
	
	/**
	 * Singleton method
	 * 
	 * @return TestManager The instance of this class in charge of running the tests
	 */
	public static function getInstance() {
		if (self::$instance) return self::$instance;
		else return self::$instance = new self();
	}
}
