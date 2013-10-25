<?php namespace spitfire\autoload;

abstract class ClassLocator
{
	public abstract function getFilenameFor($class);
	
	public function findFile($dir, $name) {
		$file = rtrim($dir, '/') . '/' . $name .'.php';
		if (file_exists($file)) { return $file; }
			
		$file = rtrim($dir, '/') . '/' . strtolower($name) .'.php';
		if (file_exists($file)) { return $file; }
		
		return false;
	}
}