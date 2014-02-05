<?php namespace spitfire\core\annotations;

use ReflectionClass;
use Strings;

class ActionReflector
{
	private $controller;
	private $action;
	
	public function __construct($controller, $method) {
		$this->controller = $controller;
		$this->action     = $method;
	}
	
	/**
	 * 
	 * @todo Needs better name
	 */
	public function execute() {
		$anotations = $this->parseAnotations();
		foreach ($anotations as $anotation) {
			$this->applyAnotation(explode(' ', $anotation));
		}
	}
	
	public function applyAnotation($annotation) {
		$annotation = array_filter($annotation);
		$method = array_shift($annotation);
		switch($method) {
			case '@method':
				return call_user_func_array(Array($this, 'method'), $annotation);
			case '@cache':
				return call_user_func_array(Array($this, 'cache' ), $annotation);
		}
	}
	
	public function parseAnotations() {
		$reflection = new ReflectionClass($this->controller);
		$method     = $reflection->getMethod($this->action);
		$docblock   = $method->getDocComment();
		
		$data  = explode("\n", $docblock);
		$count = count($data);
		$info  = array();
		
		for ($j = 0; $j < $count; $j++) {
			$line = ltrim(rtrim($data[$j]), "* \t");
			if (Strings::startsWith($line, '@')) {$info[] = $line;}
		}
		
		return $info;
	}
	
	public function method() {
		$accepted = func_get_args();
		foreach($accepted as $ok) {
			if (strtolower($ok) === strtolower($_SERVER['REQUEST_METHOD'])) {
				return;
			}
		}
		
		throw new \publicException("No valid request", 400);
	}
	
	public function cache() {
		current_context()->response->getHeaders()->set('expires', gmdate('D, d M Y H:i:s \G\M\T', strtotime(implode(' ', func_get_args()))));
	}
	
}