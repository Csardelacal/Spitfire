<?php

use \spitfire\AutoLoad;

class adminApp extends App
{
	private $beans = Array();
	private $userModel = null;
	
	public function putBean($name) {
		$this->beans[] = $name;
		return $this;
	}
	
	public function getBeans() {
		return $this->beans;
	}
	
	public function setUserModel($name) {
		$model = new $name();
		
		if ($model instanceof \spitfire\model\defaults\usermodel)
		{
			$this->userModel = $model;
		}
		return $this;
	}
	
	public function getUserModel() {
		return $this->userModel;
	}

	public function enable() {
		AutoLoad::registerClass('M3W\admin\homeController', $this->getBaseDir() . 'admin.php');
		AutoLoad::registerClass('M3W\admin\authController', $this->getBaseDir() . 'auth.php');
		
		AutoLoad::registerClass('M3W\admin\enLocale',       $this->getBaseDir() . 'locales/en.php');
		AutoLoad::registerClass('M3W\admin\esLocale',       $this->getBaseDir() . 'locales/es.php');
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

	public function getLocaleClassName($locale) {
		$className = "M3W\\admin\\{$locale}Locale";
		
		if (class_exists($className)) return $className;
		else return "M3W\\admin\\enLocale";
	}
	
	public function getClassNameSpace() {
		return 'M3W\Admin\\';
	}
}