<?php namespace spitfire\core\annotations;

use ReflectionClass;
use Strings;

/**
 * This tool allows to parse annotations for an action. An action is a method of
 * a controller, that can handle requests and therefore prepare a response. 
 * Annotations are special comments with lines which start with an @, just like
 * the author annotation on this class.
 * 
 * @author César de la Cal <cesar@magic3w.com>
 */
class ActionReflector
{
	/**
	 * The controller which's action shall be tested for annotations. It can be 
	 * either an instance of Controller or a string, with the name of the 
	 * controller class.
	 * 
	 * @var Controller|string
	 */
	private $controller;
	
	/**
	 * The name of the method that has annotations to be parsed. This allows to
	 * modify the app's behavior based on annotations which are quick and easy
	 * to write and understand.
	 * 
	 * @var string
	 */
	private $action;
	
	/**
	 * Builds a new Reflector of an action. This reflectors allow to modify the 
	 * app's behavior based on annotations to allow developers using Spitfire
	 * to modify it's behavior quickly and seamlessly.
	 * 
	 * @param Controller|string $controller
	 * @param string            $method
	 */
	public function __construct($controller, $method) {
		$this->controller = $controller;
		$this->action     = $method;
	}
	
	/**
	 * Parses the annotations for the action and modifies the behavior based on
	 * their content. This method will collect the annotations and the loop through
	 * them calling a method that executes the tasks dictated by the annotations.
	 * 
	 * @todo Needs better name
	 */
	public function execute() {
		#Collect the annotations
		$anotations = $this->parseAnotations();
		
		#Loop through the annotations and apply them
		foreach ($anotations as $anotation) {
			$this->applyAnotation(array_filter(explode(' ', $anotation)));
		}
	}
	
	/**
	 * Executes the corresponding action to a certain annotation, this receives the
	 * annotation as an array of the strings of the annotation split by spaces.
	 * 
	 * This allows versatile and fast annotation parsing that does not slow down
	 * the application.
	 * 
	 * @param string[] $annotation
	 */
	public function applyAnotation($annotation) {
		$method = array_shift($annotation);
		switch($method) {
			case '@method':
				return call_user_func_array(Array($this, 'method'), $annotation);
			case '@cache':
				return call_user_func_array(Array($this, 'cache' ), $annotation);
			case '@template':
				return call_user_func_array(Array($this, 'template' ), $annotation);
		}
	}
	
	public function getDocBlock() {
		$reflection = new ReflectionClass($this->controller);
		$method     = $reflection->getMethod($this->action);
		
		return $method->getDocComment();
	}
	
	/**
	 * Fetches the annotations for the action so they can be used to establish
	 * the way the app behaves.
	 * 
	 * @return string[]
	 */
	public function parseAnotations() {
		$docblock = $this->getDocBlock();
		$data  = explode("\n", $docblock);
		$count = count($data);
		$info  = array();
		
		for ($j = 0; $j < $count; $j++) {
			$line = ltrim(rtrim($data[$j]), "* \t");
			if (Strings::startsWith($line, '@')) {$info[] = $line;}
		}
		
		return $info;
	}
	
	protected function method() {
		$accepted = func_get_args();
		foreach($accepted as $ok) {
			if (strtolower($ok) === strtolower($_SERVER['REQUEST_METHOD'])) {
				return;
			}
		}
		
		throw new \publicException("No valid request", 400);
	}
	
	protected function cache() {
		current_context()
			->response
			->getHeaders()
			->set('expires', gmdate('D, d M Y H:i:s \G\M\T', strtotime(implode(' ', func_get_args()))));
	}
	
	protected function template() {
		
		$file = implode(' ', func_get_args());
		
		if ($file === 'none') {
			return current_context()->view->setRenderTemplate(false);
		}
		
		current_context()->view->setFile($file);
	}
	
}