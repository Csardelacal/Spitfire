<?php namespace spitfire\autoload;

/**
 * @deprecated since version 0.1-dev 20141211
 */
class SystemClassLocator extends ClassLocator
{
	
	public function getBaseDir() {
		return '';
	}
	
	public function getFilenameFor($class) {
		$data = explode('\\', $class);
		$name = array_pop($data);
		$dir  = rtrim($this->getBaseDir(), '/') . implode(DIRECTORY_SEPARATOR, $data);
		
		return $this->findFile($dir, $name);
	}

}

