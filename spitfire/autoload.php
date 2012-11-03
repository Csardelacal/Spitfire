<?php

class _SF_AutoLoad
{

	const   APPCONTROLLER_LOCATION = 'bin/controllers/appController.php';
	const   CONTROLLER_DIRECTORY   = 'bin/controllers/';
	const   COMPONENT_DIRECTORY    = 'bin/components/';
	const   UI_COMPONENT_DIRECTORY = 'bin/ui/';
	const   STD_CLASS_DIRECTORY    = 'bin/classes/';

	static $instance = false;

	private $registered_classes = Array();

	public function __construct() {
		self::$instance = $this;
		spl_autoload_register(Array($this, 'retrieveClass'));
	}

	public function register($className, $location) {
		$className = strtolower($className);
		$this->registered_classes[$className] = $location;
	}

	public function retrieveClass($className) {

		if (SpitFire::$debug) SpitFire::$debug->msg("Imported class $className");

		#Case insensitive please!
		$className = strtolower($className);

		#Check if the class has been registered
		if ( isset($this->registered_classes[$className]) ) {
			include $this->registered_classes[$className];
			return true;
		}

		#Check if the class requested is a controller
		if (strpos($className, 'controller')) {
			$filename = str_replace('controller', '', $className);
			$filename = self::CONTROLLER_DIRECTORY . $filename . '.php';
			if (file_exists($filename)) {
				include $filename;
				return true;
			}
		}

		#Check if the class requested is a component
		if (strpos($className, 'component')) {
			$filename = str_replace('component', '', $className);
			$filename = self::COMPONENT_DIRECTORY . $filename . '.php';
			if (file_exists($filename)) {
				include $filename;
				return true;
			}
		}

		#Check if the class requested is a component
		if (strpos($className, 'component')) {
			$filename = str_replace('component', '', $className);
			$filename = self::UI_COMPONENT_DIRECTORY . $filename . '.php';
			if (file_exists($filename)) {
				include $filename;
				return true;
			}
		}

		#Check if the class requested is a normal class
		$filename = $className;
		$filename = self::STD_CLASS_DIRECTORY . $filename . '.php';
		if (file_exists($filename)) {
			include $filename;
			return true;
		}

		#Check if the class to be registered is AppController
		if ($className == 'appcontroller') {
			if (file_exists(self::APPCONTROLLER_LOCATION)) {
				include self::APPCONTROLLER_LOCATION;
				return true;
			}
			else include 'spitfire/appcontroller.php';
		}

		error_log("Class $className not found.");

	}

	#######STATIC METHODS#####################

	public static function registerClass($class, $location) {
		self::$instance->register($class, $location);
	}

}