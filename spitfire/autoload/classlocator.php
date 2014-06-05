<?php namespace spitfire\autoload;

/**
 * A class locator is a tool to tell Spitfire where classes are located. This 
 * usually isn't needed for developers with simple applications, as the base 
 * locators loaded beforehand by Spitfire are able to locate most of the common
 * usage classes.
 * 
 * But if your app uses special ways of handling file locations for classes you
 * will need to create your custom Locator and tell Spitfire where the files are
 * located.
 * 
 * @author César de la Cal <cesar@magic3w.com>
 * @last-revision 2013-10-30
 */
abstract class ClassLocator
{
	/**
	 * This method is the main tool of every locator. The implementing class will
	 * receive a fully qualified name and can either return the filename where 
	 * it is located in or return false
	 * 
	 * @param string|boolean $class The name of the class (fully qualified)
	 */
	public abstract function getFilenameFor($class);
	
	/**
	 * This method will check if there exists a file with the name specified inside
	 * a directory and if not it will check if the lowercased of the filename
	 * does exist. 
	 * 
	 * @param string $dir
	 * @param string $name
	 * @return string|boolean
	 */
	public function findFile($dir, $name, $suffix = null) {
		$sf = spitfire();
		
		$file = $sf->getCWD() . '/' . rtrim($dir, '/') . '/' . $name .'.php';
		if (file_exists($file)) { return $file; }
			
		$file = $sf->getCWD() . '/' . rtrim($dir, '/') . '/' . strtolower($name) .'.php';
		if (file_exists($file)) { return $file; }
		
		$file = $sf->getCWD() . '/' . rtrim($dir, '/') . '/' . strtolower($name) . '-' . strtolower($suffix) .'.php';
		if ($suffix && file_exists($file)) { return $file; }
		
		return false;
	}
	
	/**
	 * Defines this ClassLocator as part of autoload. In oredr to have autoload 
	 * send classname requests to this file you will need to call this function
	 * so it is registered.
	 */
	public function registerLocator() {
		\spitfire\AutoLoad::getInstance()->registerLocator($this);
	}
}