<?php

namespace spitfire;

class AutoLoad
{

	const   APPCONTROLLER_LOCATION = 'bin/controllers/appController.php';
	const   CONTROLLER_DIRECTORY   = 'bin/controllers/';
	const   VIEW_DIRECTORY         = 'bin/viewControllers/';
	const   COMPONENT_DIRECTORY    = 'bin/components/';
	const   MODEL_DIRECTORY        = 'bin/models/';
	const   LOCALE_DIRECTORY       = 'bin/locales/';
	const   BEAN_DIRECTORY         = 'bin/beans/';
	const   APP_DIRECTORY          = 'bin/apps/';
	const   UI_COMPONENT_DIRECTORY = 'bin/ui/';
	const   STD_CLASS_DIRECTORY    = 'bin/classes/';
	const   CLASS_EXTENSION        = '.php';

	static $instance = false;

	private $spitfire;
	private $registered_classes = Array();

	public function __construct($spitfire) {
		self::$instance = $this;
		$this->spitfire = $spitfire;
		spl_autoload_register(Array($this, 'retrieveClass'));
	}

	public function register($className, $location) {
		$className = $className;
		$this->registered_classes[$className] = $location;
	}

	public function retrieveClass($className) {

		$this->spitfire->log("Imported class $className");
		
		if (isset($this->registered_classes[$className]))
			return include $this->registered_classes[$className];
		
		$class = new ClassInfo($className);
		
		switch($class->getType()) {
			case ClassInfo::TYPE_CONTROLLER:
				$filename = self::CONTROLLER_DIRECTORY .
						(($class->getNameSpace())?implode(DIRECTORY_SEPARATOR, $class->getNameSpace()):'') .
						(($class->getNameSpace())?DIRECTORY_SEPARATOR:'') .
						$class->getClassName() . 
						self::CLASS_EXTENSION;
				if (file_exists($filename)) return include $filename;
				
				$filename = self::CONTROLLER_DIRECTORY .
						(($class->getNameSpace())?implode(DIRECTORY_SEPARATOR, $class->getNameSpace()):'') .
						(($class->getNameSpace())?DIRECTORY_SEPARATOR:'') .
						strtolower($class->getClassName()) . 
						self::CLASS_EXTENSION;
				if (file_exists($filename)) return include $filename;
				
				break;
				
			case ClassInfo::TYPE_VIEW:
				$filename = self::VIEW_DIRECTORY .
						(($class->getNameSpace())?implode(DIRECTORY_SEPARATOR, $class->getNameSpace()):'') .
						(($class->getNameSpace())?DIRECTORY_SEPARATOR:'') .
						$class->getClassName() . 
						self::CLASS_EXTENSION;
				if (file_exists($filename)) return include $filename;
				
				$filename = self::VIEW_DIRECTORY .
						(($class->getNameSpace())?implode(DIRECTORY_SEPARATOR, $class->getNameSpace()):'') .
						(($class->getNameSpace())?DIRECTORY_SEPARATOR:'') .
						strtolower($class->getClassName()) . 
						self::CLASS_EXTENSION;
				if (file_exists($filename)) return include $filename;
				
				break;
				
			case ClassInfo::TYPE_COMPONENT:
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
				
				break;
				
			case ClassInfo::TYPE_MODEL:
				$filename = self::MODEL_DIRECTORY .
						$class->getClassName() .
						self::CLASS_EXTENSION;
				if (file_exists($filename)) return include $filename;
				
				$filename = self::MODEL_DIRECTORY .
						strtolower($class->getClassName()) .
						self::CLASS_EXTENSION;
				if (file_exists($filename)) return include $filename;
				
				break;
				
			case ClassInfo::TYPE_BEAN:
				$filename = self::BEAN_DIRECTORY .
						$class->getClassName() .
						self::CLASS_EXTENSION;
				if (file_exists($filename)) return include $filename;
				
				$filename = self::BEAN_DIRECTORY .
						strtolower($class->getClassName()) .
						self::CLASS_EXTENSION;
				if (file_exists($filename)) return include $filename;
				
				break;
				
			case ClassInfo::TYPE_APP:
				$filename = self::APP_DIRECTORY .
						$class->getClassName() .
						DIRECTORY_SEPARATOR.
						'main' .
						self::CLASS_EXTENSION;
				if (file_exists($filename)) return include $filename;
				
				$filename = self::APP_DIRECTORY .
						strtolower($class->getClassName()) .
						DIRECTORY_SEPARATOR.
						'main' .
						self::CLASS_EXTENSION;
				if (file_exists($filename)) return include $filename;
				
				break;
				
			case ClassInfo::TYPE_LOCALE:
				$filename = self::LOCALE_DIRECTORY .
						$class->getClassName() .
						self::CLASS_EXTENSION;
				if (file_exists($filename)) return include $filename;
				
				$filename = self::LOCALE_DIRECTORY .
						strtolower($class->getClassName()) .
						self::CLASS_EXTENSION;
				if (file_exists($filename)) return include $filename;
				
				break;
				
			case ClassInfo::TYPE_STDCLASS:
				$filename = self::STD_CLASS_DIRECTORY .
						$class->getClassName() .
						self::CLASS_EXTENSION;
				if (file_exists($filename)) return include $filename;
				
				$filename = self::STD_CLASS_DIRECTORY .
						strtolower($class->getClassName()) .
						self::CLASS_EXTENSION;
				if (file_exists($filename)) return include $filename;
				
				break;
				
		}
		
		$this->spitfire->log(".... failed! No class $className");

	}

	#######STATIC METHODS#####################

	public static function registerClass($class, $location) {
		self::$instance->register($class, $location);
	}

}