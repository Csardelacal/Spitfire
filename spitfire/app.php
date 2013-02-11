<?php

use spitfire\View;

abstract class App
{
	public $view;
	public $controller;
	public $basedir;
	
	public function __construct($basedir) {
		$this->basedir = $basedir;
	}
	
	public function runTask($controller, $action, $object) {
		#Create a controller
		$controllerClass = $controller . 'Controller';
		if (!class_exists($controllerClass)) throw new publicException('Page not found', 404);
		$this->controller = new $controllerClass($this);
		
		#Create a view
		$viewClass = $controller . 'View';
		if (class_exists($viewClass)) $this->view = new $viewClass($this);
		else $this->view = new View($this);
		
		#Run the onload
		if (method_exists($this->controller, 'onload') ) 
			call_user_func_array(Array($this->controller, 'onload'), Array($action));
		
		#Check if the controller can handle the request
		$request = Array($this->controller, $action);
		if (is_callable($request)) call_user_func_array($request, $object);
		else throw new publicException('Action not found', 404);
	}
	
	abstract public function enable();
	abstract public function getAssetsDirectory();
	abstract public function getTemplateDirectory();
	
}