<?php namespace spitfire\autoload;

use Strings;
use spitfire\ClassInfo;

class AppClassLocator extends ClassLocator
{
	/**
	 * Gets the filename for the expected App. This allows the system to find 
	 * the required file where the class is contained to be included and used.
	 *
	 * @param string $class The complete name of the class
	 */
	public function getFilenameFor($class) {
		if ($class === true || $class === false) {
			return false;
		}

		if (Strings::endsWith($class, 'App')) {
			$classURI = explode('\\', substr($class, 0, 0 - strlen('app')));
			$filename = 'main';
			$dir = spitfire()->getDirectory(ClassInfo::TYPE_APP) . implode(DIRECTORY_SEPARATOR, $classURI);
			return $this->findFile($dir, $filename);
		}
		return false;
	}

}
