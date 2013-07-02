<?php

namespace spitfire;

class AutoLoad
{

	const   CONTROLLER_DIRECTORY   = 'bin/controllers/';
	const   VIEW_DIRECTORY         = 'bin/views/';
	const   COMPONENT_DIRECTORY    = 'bin/components/';
	const   MODEL_DIRECTORY        = 'bin/models/';
	const   RECORD_DIRECTORY       = 'bin/types/';
	const   LOCALE_DIRECTORY       = 'bin/locales/';
	const   BEAN_DIRECTORY         = 'bin/beans/';
	const   APP_DIRECTORY          = 'bin/apps/';
	const   UI_COMPONENT_DIRECTORY = 'bin/ui/';
	const   STD_CLASS_DIRECTORY    = 'bin/classes/';
	const   CLASS_EXTENSION        = '.php';
	
	private $directories = Array(
	    ClassInfo::TYPE_CONTROLLER => self::CONTROLLER_DIRECTORY,
	    ClassInfo::TYPE_APP        => self::APP_DIRECTORY,
	    ClassInfo::TYPE_BEAN       => self::BEAN_DIRECTORY,
	    ClassInfo::TYPE_COMPONENT  => self::COMPONENT_DIRECTORY,
	    ClassInfo::TYPE_LOCALE     => self::LOCALE_DIRECTORY,
	    ClassInfo::TYPE_MODEL      => self::MODEL_DIRECTORY,
	    ClassInfo::TYPE_RECORD     => self::RECORD_DIRECTORY,
	    ClassInfo::TYPE_VIEW       => self::VIEW_DIRECTORY,
	    ClassInfo::TYPE_STDCLASS   => self::STD_CLASS_DIRECTORY
	);

	static $instance = false;

	private $spitfire;
	private $registered_classes = Array();
	private $imported_classes   = Array();

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
		
		#If there is a request, retrieve the app which is handling it
		if (null != $request = $this->spitfire->getRequest()) 
			$app = $request->getApp();
		
		if (!isset($app) || $app === $this->spitfire || strpos($className, $app->getNameSpace()) !== 0) 
			$app = $this->spitfire;
		
		if (in_array($className, $this->imported_classes)) return;
		$this->imported_classes[] = $className;
		$this->spitfire->log("Imported class $className");
		
		if (isset($this->registered_classes[$className]))
			return include $this->registered_classes[$className];
		
		$class = new ClassInfo($className, $app);
		$dir   = $app->getDirectory($class->getType());
		
		if ($class->getType() == ClassInfo::TYPE_APP) {
			
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
		}
		
		$filename = $dir . 
			(($class->getNameSpace())?implode(DIRECTORY_SEPARATOR, $class->getNameSpace()):'') .
			(($class->getNameSpace())?DIRECTORY_SEPARATOR:'') .
			$class->getClassName() . 
			self::CLASS_EXTENSION;
		
		if (file_exists($filename)) return include $filename;
		
		$filename = $dir . 
			(($class->getNameSpace())?implode(DIRECTORY_SEPARATOR, $class->getNameSpace()):'') .
			(($class->getNameSpace())?DIRECTORY_SEPARATOR:'') .
			strtolower($class->getClassName()) . 
			self::CLASS_EXTENSION;
		
		if (file_exists($filename)) return include $filename;
		
		$filename = $dir . 
			(($class->getNameSpace())?implode(DIRECTORY_SEPARATOR, $class->getNameSpace()):'') .
			(($class->getNameSpace())?DIRECTORY_SEPARATOR:'') .
			strtolower($class->getClassName()) . '-' . strtolower($class->getType()) .
			self::CLASS_EXTENSION;
		
		if (file_exists($filename)) return include $filename;
		
		$filename = $dir . 
			(($class->getNameSpace())?implode(DIRECTORY_SEPARATOR, $class->getNameSpace()):'') .
			(($class->getNameSpace())?DIRECTORY_SEPARATOR:'') .
			$class->getClassName() . 
			DIRECTORY_SEPARATOR .
			'main' .
			self::CLASS_EXTENSION;
		
		if (file_exists($filename)) return include $filename;
		
		$this->spitfire->log(".... failed! No class $className");

	}

	#######STATIC METHODS#####################

	public static function registerClass($class, $location) {
		self::$instance->register($class, $location);
	}

}