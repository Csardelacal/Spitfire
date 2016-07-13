<?php namespace spitfire\core\annotations;

use ReflectionClass;
use Exception;

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
		$parser     = new AnnotationParser();
		$anotations = $parser->parse($this->getDocBlock());
		
		#Loop through the annotations and apply them
		foreach ($anotations as $annotation => $entries) {
			$this->applyAnotation($annotation, $entries[0]);
		}
	}
	
	/**
	 * Executes the corresponding action to a certain annotation, this receives the
	 * annotation as an array of the strings of the annotation split by spaces.
	 * 
	 * This allows versatile and fast annotation parsing that does not slow down
	 * the application.
	 * 
	 * @todo Convert this whole ordeal into something more flexible and usable
	 * @param string[] $annotation
	 */
	public function applyAnotation($method, $annotation) {
		
		switch($method) {
			case 'request-method':
				return call_user_func_array(Array($this, 'method'), $annotation);
			case 'cache':
				return call_user_func_array(Array($this, 'cache' ), $annotation);
			case 'template':
				return call_user_func_array(Array($this, 'template' ), $annotation);
			case 'layout':
				return call_user_func_array(Array($this, 'layout' ), $annotation);
			/*
			 * Stuff that still is in the works
			 * * Route
			 */
		}
	}
	
	/**
	 * Fetches the docblock of the action by creating a reflection of the class,
	 * the action method and then requesting it's docblock. Guess there is no more
	 * straightforward way to do this currently.
	 * 
	 * @return string
	 */
	public function getDocBlock() {
		$reflection = new ReflectionClass($this->controller);
		
		try {
			$method = $reflection->getMethod($this->action);
		} catch (Exception $ex) {
			try {$method = $reflection->getMethod('__call');}
			catch (Exception $i) {throw $ex;}
		}
		
		return $method->getDocComment();
	}
	
	/**
	 * Checks whether the request being sent by the user is acceptable for the 
	 * selected action. This allows your application to set a bunch of valid methods
	 * that will avoid this one throwing an exception informing about the invalid
	 * request.
	 * 
	 * @return mixed
	 * @throws \publicException If the user is throwing a request with one method
	 *			that is not accepted.
	 */
	protected function method() {
		$accepted = func_get_args();
		foreach($accepted as $ok) {
			if (strtolower($ok) === strtolower($_SERVER['REQUEST_METHOD'])) {
				return;
			}
		}
		
		throw new \publicException("No valid request", 400);
	}
	
	/**
	 * Sets the time the current Request is still valid. This is especially useful
	 * to reduce server load and increase performance when applied to requests 
	 * that are not expected to change in foreseeable time, especially big requests
	 * like images.
	 */
	protected function cache() {
		current_context()
			->response
			->getHeaders()
			->set('expires', gmdate('D, d M Y H:i:s \G\M\T', strtotime(implode(' ', func_get_args()))));
	}
	
	/**
	 * Defines whether the current template is rendered or not and what file is
	 * used for that purpose. This allows your application to quickly define
	 * templates that are not located in normal locations.
	 * 
	 * @return mixed
	 */
	protected function template() {
		
		$file = implode(' ', func_get_args());
		
		if ($file === 'none') {
			return current_context()->view->setRenderTemplate(false);
		}
		
		current_context()->view->setFile($file);
	}
	
	/**
	 * Defines whether the current template is rendered or not with a layout
	 * and what file is used for that purpose. This allows your application to 
	 * quickly define templates that are not located in normal locations.
	 * 
	 * @return mixed
	 */
	protected function layout() {
		
		$file = implode(' ', func_get_args());
		
		if ($file === 'none') {
			return current_context()->view->setRenderLayout(false);
		}
		
		current_context()->view->setLayoutFile($file);
	}
	
}