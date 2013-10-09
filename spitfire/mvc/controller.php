<?php

use spitfire\MemcachedAdapter;
use spitfire\Context;
use spitfire\MVC;

abstract class Controller extends MVC
{
	
	//abstract public function index ($object, $params);
	//abstract public function detail ($object, $params);
	
	public function __construct(Context$intent) {

		parent::__construct($intent);

		$this->memcached = MemcachedAdapter::getInstance();
		$this->call      = new _SF_Invoke();
	}
	
	public function __call($name, $arguments) {
		$request = spitfire()->getRequest();
		$controller = $request->getControllerURI();
		$action = $name;
		$object = $arguments;
		
		if (class_exists( implode('\\', $controller) . '\\' . $action . 'Controller')) {
			array_push($controller, $action);
			$action     = array_shift($object);
			
			$controllerName = implode('\\', $controller) . 'Controller';
			$request->setController(new $controllerName($request->getApp()));
			$request->setAction($action);
			$request->setObject($object);
			$request->handle();
		}
		else {
			throw new publicException("Page not found", 404, new privateException('Action not found', 0));
		}
	}
	
}