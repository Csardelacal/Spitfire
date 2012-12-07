<?php

class _SF_AutoLoad
{

	const   APPCONTROLLER_LOCATION = 'bin/controllers/appController.php';
	const   CONTROLLER_DIRECTORY   = 'bin/controllers/';
	const   COMPONENT_DIRECTORY    = 'bin/components/';
	const   UI_COMPONENT_DIRECTORY = 'bin/ui/';
	const   STD_CLASS_DIRECTORY    = 'bin/classes/';
	const   CLASS_EXTENSION        = '.php';

	static $instance = false;

	private $registered_classes = Array();

	public function __construct() {
		self::$instance = $this;
		spl_autoload_register(Array($this, 'retrieveClass'));
	}

	public function register($className, $location) {
		$className = $className;
		$this->registered_classes[$className] = $location;
	}

	public function retrieveClass($className) {

		if (SpitFire::$debug) SpitFire::$debug->msg("Imported class $className");
		
		if (isset($this->registered_classes[$className]))
			return include $this->registered_classes[$className];
		
		$class = new _SF_Class($className);
		
		switch($class->getType()) {
			case _SF_Class::TYPE_CONTROLLER:
				$filename = self::CONTROLLER_DIRECTORY .
						(($class->getNameSpace())?implode(DIRECTORY_SEPARATOR, $class->getNameSpace()):'') .
						(($class->getNameSpace())?DIRECTORY_SEPARATOR:'') .
						$class->getClassName() . 
						self::CLASS_EXTENSION;
				error_log($filename);
				if (file_exists($filename)) return include $filename;
				
				$filename = self::CONTROLLER_DIRECTORY .
						(($class->getNameSpace())?implode(DIRECTORY_SEPARATOR, $class->getNameSpace()):'') .
						(($class->getNameSpace())?DIRECTORY_SEPARATOR:'') .
						strtolower($class->getClassName()) . 
						self::CLASS_EXTENSION;
				if (file_exists($filename)) return include $filename;
				
			case _SF_Class::TYPE_COMPONENT:
				$filename = self::COMPONENT_DIRECTORY .
						implode(DIRECTORY_SEPARATOR, $class->getNameSpace()) .
						DIRECTORY_SEPARATOR .
						$class->getClassName() .
						DIRECTORY_SEPARATOR .
						'main' .
						self::CLASS_EXTENSION;
				if (file_exists($filename)) return include $filename;
				
				$filename = self::COMPONENT_DIRECTORY .
						implode(DIRECTORY_SEPARATOR, $class->getNameSpace()) .
						DIRECTORY_SEPARATOR .
						strtolower($class->getClassName()) . 
						DIRECTORY_SEPARATOR .
						'main' .
						self::CLASS_EXTENSION;
				if (file_exists($filename)) return include $filename;
				
		}
		
		error_log('Class ' . $className . ' not found');

	}

	#######STATIC METHODS#####################

	public static function registerClass($class, $location) {
		self::$instance->register($class, $location);
	}

}