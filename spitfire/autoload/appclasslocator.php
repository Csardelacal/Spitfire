<?php namespace spitfire\autoload;

use Strings;
use spitfire\ClassInfo;

class AppClassLocator extends ClassLocator
{
	
	public function getFilenameFor($class) {
		
		if (Strings::endsWith($class, 'App')) {
			$classURI = explode('\\', substr($class, 0, 0 - strlen('app')));
			$filename = 'main';
			$dir = spitfire()->getDirectory(ClassInfo::TYPE_APP) . implode(DIRECTORY_SEPARATOR, $classURI);
			return $this->findFile($dir, $filename);
		}
		return false;
	}

}
