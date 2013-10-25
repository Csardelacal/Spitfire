<?php namespace spitfire\autoload;

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

