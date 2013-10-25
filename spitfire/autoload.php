<?php namespace spitfire;

use spitfire\autoload\RegisteredClassLocator;
use spitfire\autoload\UserClassLocator;

class AutoLoad
{

	static $instance = false;

	private $spitfire;
	private $imported_classes   = Array();
	private $locators           = Array();

	public function __construct($spitfire) {
		self::$instance = $this;
		$this->spitfire = $spitfire;
		spl_autoload_register(Array($this, 'retrieveClass'));
		
		$this->makeLocators();
	}
	
	public function makeLocators() {
		$this->locators[] = new autoload\SystemClassLocator();
		$this->locators[] = new RegisteredClassLocator();
		$this->locators[] = new UserClassLocator('Controller', ClassInfo::TYPE_CONTROLLER);
		$this->locators[] = new autoload\AppClassLocator();
		$this->locators[] = new UserClassLocator('Model',      ClassInfo::TYPE_MODEL);
		$this->locators[] = new UserClassLocator('Locale',     ClassInfo::TYPE_LOCALE);
		$this->locators[] = new UserClassLocator('View',       ClassInfo::TYPE_VIEW);
		$this->locators[] = new UserClassLocator('Bean',       ClassInfo::TYPE_BEAN);
		$this->locators[] = new UserClassLocator('',           ClassInfo::TYPE_STDCLASS);
	}

	public function register($className, $location) {
		RegisteredClassLocator::register($className, $location);
	}

	public function retrieveClass($className) {
		foreach ($this->locators as $locator) {
			if (false !== $file = $locator->getFilenameFor($className)) {
				include $file;
				$this->spitfire->log("Imported class $className...");
				return true;
			}
		}
		return false;
	}

	#######STATIC METHODS#####################

	public static function registerClass($class, $location) {
		self::$instance->register($class, $location);
	}

}