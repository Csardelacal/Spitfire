<?php namespace spitfire\autoload;

use Strings;

/**
 * This class allows to assign a Namespace to a directory. This allows your 
 * application to listen to all the requests coming for classes with a certain
 * namespace and verifying if your application can provide the requested class.
 */
class NamespacedClassLocator extends ClassLocator
{
	/**
	 * This is the symbol used to separate the elements of a namespace. This allows
	 * Spitfire to break the fully qualified class names into smaller chunks it can
	 * work with.
	 */
	const NAMESPACE_SEPARATOR = '\\';
	
	/**
	 * The namespace in which this Locator tries to provide classes. If the FQCN 
	 * of the class doesn't start with this the locator will skip looking for 
	 * a file to locate this class in.
	 * @var string 
	 */
	private $namespace;
	
	/**
	 * The directory mapped to this namespace. In case the namespace is matched the
	 * file is looked after inside the current directory. 
	 * 
	 * Please note that if it matches a sub-namespace it will try to look up inside
	 * a subdirectory of the namespace.
	 * @var string 
	 */
	private $directory;
	
	/**
	 * This is the type of class the Locator is looking after. In case the application
	 * specified a suffix for a special class type (like f.e. Controllers) the 
	 * framework may have to look in a special directory.
	 * 
	 * @var string
	 */
	private $suffix;
	
	/**
	 * Binds a namespace to a directory. This allows your application to define a 
	 * directory to look after a certain kind of classes.
	 * 
	 * Note that this doesn't make collission impossible. So you may happen to 
	 * create a class that has conflictive namespaces settings with already existing
	 * ones.
	 * 
	 * @param string $namespace
	 * @param string $dir
	 * @param string $suffix
	 */
	public function __construct($namespace, $dir, $suffix = '') {
		$this->namespace = trim($namespace,self::NAMESPACE_SEPARATOR) . self::NAMESPACE_SEPARATOR;
		$this->directory = $dir;
		$this->suffix    = $suffix;
	}
	
	/**
	 * Checks if this class is found inside this namespace. This will check if the 
	 * namespace is alright and if there is a file that matches the class.
	 * 
	 * @param string $classname
	 * @return string|boolean
	 */
	public function getFilenameFor($classname) {
		$class = trim($classname, '\\');
		#If the namespace and the suffix match we test if the file is found.
		if (Strings::startsWith($class, $this->namespace) && Strings::endsWith($class, $this->suffix)) {
			
			#Get the path WITHIN the namespace that locates the file
			if (strlen($this->suffix)) {
				$path = explode(self::NAMESPACE_SEPARATOR, substr($class, strlen($this->namespace), 0 - strlen($this->suffix)));
			} else {
				$path = explode(self::NAMESPACE_SEPARATOR, substr($class, strlen($this->namespace)));
			}
			
			#Extract the filename and merge the Path to the namespace with the path within
			$file = array_pop($path);
			$directory = array_merge(explode(DIRECTORY_SEPARATOR, $this->directory), $path);
			#Check for the file
			return $this->findFile(implode(DIRECTORY_SEPARATOR, $directory), $file, $this->suffix);
		}
		
		return false;
	}

}