<?php namespace spitfire\autoload;

use Strings;

class UserClassLocator extends ClassLocator
{
	private $suffix;
	private $contenttype;

	public function __construct($suffix, $contenttype) {
		$this->suffix = $suffix;
		$this->contenttype = $contenttype;
	}
	
	public function getFilenameFor($class) {
		$app = spitfire()->findAppForClass($class);
		if ($app && Strings::endsWith($class, $this->suffix)) {
			if (!$this->suffix) {$classURI = explode('\\', substr($class, strlen($app->getNameSpace())));}
			else { $classURI = explode('\\', substr($class, strlen($app->getNameSpace()), 0 - strlen($this->suffix))); }
			$filename = array_pop($classURI);
			$dir = $app->getDirectory($this->contenttype) . implode(DIRECTORY_SEPARATOR, $classURI);
			return $this->findFile($dir, $filename);
		}
		return false;
	}

}
