<?php namespace spitfire\autoload;

use Strings;

/**
 * This locators are meant to find things inside the scope of an application
 * including Spitfire itself. Classes like controllers, views and models should
 * be all found by this classes without any issue.
 * 
 * I've been trying for quite a while to find a good way to extract the information
 * about a class from it's name and analyze it inside a class. Something like one
 * which would provide infromation about the suffix being used and stuff.
 * 
 * The problem there is that class locating idoes only happen once in the cycle of
 * an app. And a class trying to fetch info about another would just make (did make)
 * the process unnecessarily complicated.
 * 
 * @deprecated since version 0.1-dev
 * @author César de la Cal <cesar@magic3w.com>
 * @last-revision 2013-10-30
 */
class UserClassLocator extends ClassLocator
{
	/**
	 * The class suffix that indicates the type of the class. Like Controller, View
	 * or Model.
	 * 
	 * @var string
	 */
	private $suffix;
	
	/**
	 * The type of content the app is looking for. This is important to request 
	 * from the app that owns the namespace information about the directory where
	 * it stores said classes.
	 *
	 * @var mixed
	 */
	private $contenttype;
	
	/**
	 * Creates a new class locator for user classes. In Spitfire those are the ones
	 * that are used by the developers to alter it's behavior, like controllers,
	 * views and models.
	 * 
	 * @param string $suffix
	 * @param mixed $contenttype
	 * @param string $basedir
	 */
	public function __construct($suffix, $contenttype, $basedir) {
		$this->suffix = $suffix;
		$this->contenttype = $contenttype;
		
		parent::__construct($basedir);
	}
	
	/**
	 * Gets the filename where a class is located or returns false if the class 
	 * could not be found. For USerClassLocators this means finding the app for 
	 * the class it is searching for and then finding it inside the app's directory.
	 * 
	 * @param string $class
	 * @return string|boolean
	 */
	public function getFilenameFor($class) {
		$app = spitfire()->findAppForClass($class);
		
		if ($app && Strings::endsWith($class, $this->suffix)) {
			$classURI = $this->getClassURI($class, $app);
			$filename = array_pop($classURI);
			$dir = $app->getDirectory($this->contenttype) . implode(DIRECTORY_SEPARATOR, $classURI);
			return $this->findFile($dir, $filename, $this->suffix);
		}
		return false;
	}
	
	/**
	 * The class URI is the name of the class without the leading ap inherited
	 * namespace and without the suffix (in case the class has one). This function
	 * will return this information already fragmented into an array so it can be
	 * imploded with the DIRECTORY_SEPARATOR easily
	 * 
	 * @param string $class
	 * @param \App $app
	 * @return type
	 */
	public function getClassURI ($class, $app) {
		if (!$this->suffix) {
			return explode('\\', substr($class, strlen($app->getNameSpace())));
		}
		else {
			return explode('\\', substr($class, strlen($app->getNameSpace()), 0 - strlen($this->suffix)));
		}
	}

}
