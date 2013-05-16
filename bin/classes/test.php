<?php

abstract class Test
{
	private $params;
	
	public function __construct() {
		$this->params = func_get_args();
	}
	
	public final function getParameters() {
		return $this->params;
	}

	protected function assertEquals($a, $b, $enforce_type = false) {
		if ($enforce_type) $result = $a === $b;
		else $result = $a == $b;
		
		$test   = get_class($this);
		$btrace = debug_backtrace();
		$method = $btrace[1];
		
		if (is_object($a)) $a = get_class($a);
		if (is_object($b)) $a = get_class($b);
		
		if (!$result) TestManager::getInstance()->error("$test::{$method['function']} > '$a' does not equal '$b'");
	}

	protected function assertInstance($object, $class) {
		$result = $object instanceof $class;
		
		$test   = get_class($this);
		$btrace = debug_backtrace();
		array_pop($btrace);
		$method = array_pop($btrace);
		
		if (is_object($object)) $object = get_class($object);
		if (is_object($class))  $class  = get_class($class);
		
		if (!$result) TestManager::getInstance()->error("$test::{$method['function']} > '$object' is not instance of '$class'");
	}
	
}