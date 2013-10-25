<?php namespace spitfire\autoload;

class RegisteredClassLocator extends ClassLocator
{
	public static $registeredClasses = Array();
	
	public function getFilenameFor($class) {
		if (isset(self::$registeredClasses[$class])) {
			return self::$registeredClasses[$class];
		}
		return false;
	}
	
	public static function register($class, $directory) {
		self::$registeredClasses[$class] = $directory;
	}
	
}