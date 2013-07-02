<?php

use spitfire\ClassInfo;

/**
 * Spitfire Application Class. This class is the base of every other 'app', an 
 * app is a wrapper of controllers (this allows to plug them into other SF sites)
 * that defines a set of rules to avoid collissions with the rest of the apps.
 * 
 * Every app resides inside of a namespace, this externally defined variable
 * defines what calls Spitfire redirects to the app.
 */
abstract class App
{
	public $view;
	public $controller;


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
	
	
	public function url($url) {
		
		if (0 === strpos($url, 'http://')) return $url;
		if (0 === strpos($url, 'https://')) return $url;
		if (0 === strpos($url, 'www.')) return 'http://' . $url;
		else return new URL($this, $url);
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
	
	public function getController($controller) {
		if (is_array($controller)) $controller = implode ('\\', $controller);
		$c = $this->getNameSpace() . $controller . 'Controller';
		$reflection = new ReflectionClass($c);
		if ($reflection->isAbstract()) 
			throw new publicException("Page not found", 404, new privateException("Abstract Controller", 0) );
		return new $c($this);
	}
	
	public function getView($controller = null) {
		
		if ($controller === null)  return $this->view;
		if (is_array($controller)) $controller = implode ('\\', $controller);
		
		$c = $this->getNameSpace() . $controller . 'View';
		if (!class_exists($c)) $c = 'spitfire\View';
		
		return new $c($this);
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
	
	abstract public function enable();
	abstract public function getNameSpace();
	abstract public function getAssetsDirectory();
	
	public function getTemplateDirectory() {
		return $this->getBaseDir() . 'templates/';
	}
	
	
	public function runTask($controller, $action, $object) {
		#Create a controller
		$this->controller = $controller;
		
		#Create a view
		$controllerName = spitfire()->getRequest()->getControllerURI();
		$this->view = $this->getView($controllerName);
		
		#Run the onload
		if (method_exists($this->controller, 'onload') ) 
			call_user_func_array(Array($this->controller, 'onload'), Array($action));
		
		#Check if the controller can handle the request
		$request = Array($this->controller, $action);
		if (is_callable($request)) call_user_func_array($request, $object);
		else throw new publicException('Page not found', 404, new privateException('Action not found', 0));
	}
	
}