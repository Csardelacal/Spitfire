<?php

use spitfire\ClassInfo;
use spitfire\Context;
use spitfire\exceptions\PrivateException;

/**
 * Spitfire Application Class. This class is the base of every other 'app', an 
 * app is a wrapper of controllers (this allows to plug them into other SF sites)
 * that defines a set of rules to avoid collissions with the rest of the apps.
 * 
 * Every app resides inside of a namespace, this externally defined variable
 * defines what calls Spitfire redirects to the app.
 * 
 * @author César de la Cal<cesar@magic3w.com>
 * @last-revision 2013-10-11
 */
abstract class App
{
	/**
	 * The basedir is the root directory of an application. For spitfire this is 
	 * usually the /bin directory. This directory contains all the app specific
	 * data. Including controllers, views and models.
	 * 
	 * In the specific case of Spitfire this folder also contains the 'child apps'
	 * that can be added to it.
	 *
	 * @var string
	 */
	private $basedir;
	private $URISpace;
	
	/**
	 * Creates a new App. Receives the directory where this app resides in
	 * and the URI namespace it uses.
	 * 
	 * @param string $basedir The root directory of this app
	 * @param string $URISpace The URI namespace it 'owns'
	 */
	public function __construct($basedir, $URISpace) {
		$this->basedir  = $basedir;
		$this->URISpace = $URISpace;
	}
	
	/**
	 * Generates a URL. This method will generate a string / URL depending on the 
	 * string being inputted.
	 * 
	 * @param Generates a URL for the current app $url
	 * @return \URL|string
	 */
	public function url($url) {
		
		if (0 === strpos($url, 'http://'))  { return $url; }
		if (0 === strpos($url, 'https://')) { return $url; }
		if (0 === strpos($url, 'www.'))     { return 'http://' . $url; }
		
		$arguments = func_get_args();
		array_unshift($arguments, $this);
		
		//Once PHP7 is stable we can use the splat (...) operator to do this
		$reflection = new ReflectionClass('\URL');
		return $reflection->newInstanceArgs($arguments);
	}
	
	public function getBaseDir() {
		return $this->basedir;
	}
	
	public function getURISpace() {
		return $this->URISpace;
	}
	
	public function getDirectory($contenttype) {
		switch($contenttype) {
			case ClassInfo::TYPE_CONTROLLER:
				return $this->getBaseDir() . 'controllers/';
			case ClassInfo::TYPE_MODEL:
				return $this->getBaseDir() . 'models/';
			case ClassInfo::TYPE_VIEW:
				return $this->getBaseDir() . 'views/';
			case ClassInfo::TYPE_LOCALE:
				return $this->getBaseDir() . 'locales/';
			case ClassInfo::TYPE_BEAN:
				return $this->getBaseDir() . 'beans/';
			case ClassInfo::TYPE_COMPONENT:
				return $this->getBaseDir() . 'components/';
			case ClassInfo::TYPE_STDCLASS:
				return $this->getBaseDir() . 'classes/';
			case ClassInfo::TYPE_APP:
				return $this->getBaseDir() . 'apps/';
		}
	}

	/**
	 * Checks if the current application has a controller with the name specified
	 * by the single argument this receives. In case a controller is found and
	 * it is not abstract the app will return the fully qualified class name of 
	 * the Controller.
	 *
	 * It should not be necessary to check the return value with the === operator
	 * as the return value on success should never be matched otherwise.
	 *
	 * @param  string $name The name of the controller being searched
	 * @return string|boolean The name of the class that has the controller
	 */
	public function hasController($name) {
		$name = (array)$name;
		$c    = $this->getNameSpace() . implode('\\', $name) . 'Controller';
		if (!class_exists($c)) { return false; }

		$reflection = new ReflectionClass($c);
		if ($reflection->isAbstract()) { return false; }
			
		return $c;
	}
	
	/**
	 * Creates a new Controller inside the context of the request. Please note 
	 * that this may throw an Exception due to the controller not being found.
	 * 
	 * @param string $controller
	 * @param Context $intent
	 * @return Controller
	 * @throws publicException
	 */
	public function getController($controller, Context$intent) {
		#Get the controllers class name. If it doesn't exist it'll be false
		$c = $this->hasController($controller);
		
		#If no controller was found, we can throw an exception letting the user know
		if ($c === false) { throw new publicException("Page not found", 404, new PrivateException("Controller not fond", 0) ); }
		
		#Otherwise we will instantiate the class and return it
		return new $c($intent);
	}
	
	public function getControllerURI($controller) {
		return explode('\\', substr(get_class($controller), strlen($this->getNameSpace()), 0-strlen('Controller')));
	}
	
	public function getView(Controller$controller) {
		
		$name = implode('\\', $this->getControllerURI($controller));
		
		$c = $this->getNameSpace() . $name . 'View';
		if (!class_exists($c)) $c = 'spitfire\View';
		
		return new $c($controller->context);
	}
	
	public function getControllerDirectory() {
		return $this->getBaseDir() . 'controllers/';
	}
	
	public function getLocale($locale) {
		
		if (empty($locale)) throw new privateException('Empty locale');
		
		$c = $this->getNameSpace() . $locale . 'Locale';
		
		if (!class_exists($c)) {
			throw new privateException('Not a valid locale for this app');
		}
		
		$reflection = new ReflectionClass($c);
		if ($reflection->isAbstract()) throw new publicException("Page not found", 404, new privateException("Abstract Locale", 0) );
		return new $c();
	}
	
	public function createRoutes() {
		$ns       = $this->URISpace? '/' . $this->URISpace : '';
		$uriSpace = $this->URISpace;
		
		\spitfire\core\router\Router::getInstance()->request($ns, function (spitfire\core\router\Parameters$params) use ($uriSpace) {
			$args = $params->getUnparsed();
			return new \spitfire\core\Path($uriSpace, array_shift($args), array_shift($args), $args);
		});
	}
	
	abstract public function enable();
	abstract public function getNameSpace();
	abstract public function getAssetsDirectory();
	

	/**
	 * Returns the directory the templates are located in. This function should be 
	 * avoided in favor of the getDirectory function.
	 * 
	 * @deprecated since version 0.1-dev 20150423
	 */
	public function getTemplateDirectory() {
		return $this->getBaseDir() . 'templates/';
	}
	
}
