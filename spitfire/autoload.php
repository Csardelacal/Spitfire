<?php namespace spitfire;

use spitfire\autoload\RegisteredClassLocator;
use spitfire\exceptions\ExceptionHandler;
use spitfire\autoload\NamespacedClassLocator;

class AutoLoad
{

	static $instance            = null;

	private $imported_classes   = Array();
	private $locators           = Array();

	public function __construct($basedir) {
		self::$instance = $this;
		spl_autoload_register(Array($this, 'retrieveClass'));
		
		$this->makeLocators($basedir);
	}
	
	public function makeLocators($basedir) {
		$this->locators[] = new NamespacedClassLocator('spitfire', $basedir . '/spitfire');
		$this->locators[] = new RegisteredClassLocator($basedir);
		$this->locators[] = new autoload\AppClassLocator($basedir);
		
		#Register the loaders for the classes within user space
		$this->locators[] = new NamespacedClassLocator('', $basedir . '/bin/controllers', 'Controller');
		$this->locators[] = new NamespacedClassLocator('', $basedir . '/bin/models',      'Model');
		$this->locators[] = new NamespacedClassLocator('', $basedir . '/bin/locales',     'Locale');
		$this->locators[] = new NamespacedClassLocator('', $basedir . '/bin/views',       'View');
		$this->locators[] = new NamespacedClassLocator('', $basedir . '/bin/beans',       'Bean');
		$this->locators[] = new NamespacedClassLocator('', $basedir . '/bin/classes');
	}

	public function register($className, $location) {
		RegisteredClassLocator::register($className, $location);
	}

	public function retrieveClass($className) {
		foreach ($this->locators as $locator) {
			if (false !== $file = $locator->getFilenameFor($className)) {
				include $file;
				$this->imported_classes[$className] = $file;
				ExceptionHandler::getInstance()->log("Imported class $className...");
				return true;
			}
		}
		return false;
	}
	
	/**
	 * 
	 * @param \spitfire\autoload\ClassLocator $locator
	 */
	public function registerLocator($locator) {
		$this->locators[] = $locator;
	}

	#######STATIC METHODS#####################

	public static function registerClass($class, $location) {
		self::$instance->register($class, $location);
	}
	
	/**
	 * Autoload uses the singleton pattern (you don't have several autoloads in 
	 * Spitfire) to find classes it needs. Use ClassLocators to add functionality
	 * to the autoload.
	 * 
	 * @return \spitfire\AutoLoad
	 */
	public static function getInstance() {
		return self::$instance;
	}

}