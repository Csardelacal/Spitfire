<?php

use \spitfire\AutoLoad;

class adminApp extends App
{
	private $beans = Array();
	
	public function putBean($name) {
		$this->beans[] = $name;
		return $this;
	}
	
	public function getBeans() {
		return $this->beans;
	}

	public function enable() {
		AutoLoad::registerClass('M3W\admin\homeController', $this->getBaseDir() . 'admin.php');
	}
	
	public function getAssetsDirectory() {
		return $this->getBaseDir() . 'assets/';
	}

	public function getTemplateDirectory() {
		return $this->getBaseDir() . 'templates/';
	}

	public function getControllerClassName($controller) {
		return 'M3W\admin\\' . $controller . 'Controller';
	}

	public function getViewClassName($controller) {
		return 'M3W\admin\\' . $controller . 'View';
	}

	public function hasController($controller) {
		return class_exists($this->getControllerClassName($controller));
	}
}