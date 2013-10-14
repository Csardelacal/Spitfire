<?php

use spitfire\Context;
use spitfire\MVC;

abstract class Controller extends MVC
{
	
	
	public function __construct(Context$intent) {
		parent::__construct($intent);
		$this->call = new _SF_Invoke();
	}
	
	/**
	 * The __call method of controllers is responsible for finding 'nested controllers'
	 * capable of handling a request. If such a controller is found Spitfire
	 * will modify the current context and handle that separately.
	 * 
	 * @link http://www.spitfirephp.com/wiki/index.php/Nested_controllers For informtion about nested controllers
	 * 
	 * @param string $name
	 * @param mixed $arguments
	 * @return Context The context that has finally solved the request.
	 * @throws publicException If there is no inhertiable controller found
	 */
	public function __call($name, $arguments) {
		$controller = $this->app->getControllerURI($this);
		$action = $name;
		$object = $arguments;
		
		if (class_exists( implode('\\', $controller) . '\\' . $action . 'Controller')) {
			array_push($controller, $action);
			$action     = array_shift($object);
			
			$controllerName = implode('\\', $controller) . 'Controller';
			$context = clone $this->context;
			$context->controller = new $controllerName($context);
			$context->action = $action;
			$request->object = $object;
			$request->view   = $this->app->getView($context->controller);
			return current_context($context)->run();
		}
		else {
			throw new publicException("Page not found", 404, new privateException('Action not found', 0));
		}
	}
	
}