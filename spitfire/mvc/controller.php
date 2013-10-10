<?php

use spitfire\Context;
use spitfire\MVC;

abstract class Controller extends MVC
{
	
	
	public function __construct(Context$intent) {
		parent::__construct($intent);
		$this->call = new _SF_Invoke();
	}
	
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
			current_context($context)->run();
			return $context;
		}
		else {
			throw new publicException("Page not found", 404, new privateException('Action not found', 0));
		}
	}
	
}