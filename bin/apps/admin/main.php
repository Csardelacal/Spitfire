<?php

class adminApp extends App
{
	public function getAssetsDirectory() {
		
	}

	public function getTemplateDirectory() {
		
	}

	public function enable() {
		\spitfire\AutoLoad::registerClass('M3W\admin\homeController', $this->getBaseDir() . 'admin.php');
		//spitfire()->registerController('admin', $this->basedir . 'admin.php', $this);
	}

	public function getControllerClassName($controller) {
		return 'M3W\admin\\' . $controller . 'Controller';
	}

	public function hasController($controller) {
		return class_exists($this->getControllerClassName($controller));
	}
}