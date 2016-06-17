<?php namespace spitfire;

use spitfire\autoload\RegisteredClassLocator;

class AutoLoad
{

	static $instance            = null;

	private $imported_classes   = Array();
	private $locators           = Array();

	public function __construct() {
		self::$instance = $this;
		spl_autoload_register(Array($this, 'retrieveClass'));
	}

	public function register($className, $location) {
		RegisteredClassLocator::register($className, $location);
	}

	public function retrieveClass($className) {
		foreach ($this->locators as $locator) {
			if (false !== $file = $locator->getFilenameFor($className)) {
				include $file;
				$this->imported_classes[$className] = $file;
				return true;
			}
		}
		echo 'No class ' . $className;
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