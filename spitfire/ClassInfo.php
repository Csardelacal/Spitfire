<?php namespace spitfire;

class ClassInfo
{
	const TYPE_CONTROLLER = 'Controller';
	const TYPE_COMPONENT  = 'Component';
	const TYPE_MODEL      = 'Model';
	const TYPE_RECORD     = 'Record';
	const TYPE_VIEW       = 'View';
	const TYPE_LOCALE     = 'Locale';
	const TYPE_APP        = 'App';
	const TYPE_BEAN       = 'Bean';
	const TYPE_STDCLASS   = '';
	
	const NAMESPACE_SEPARATOR = '\\';
	
	private $type      = '';
	private $namespace = Array();
	private $className = '';
	private $fullName  = '';
	
	private $types = Array(
	    self::TYPE_CONTROLLER,
	    self::TYPE_COMPONENT,
	    self::TYPE_MODEL,
	    self::TYPE_RECORD,
	    self::TYPE_VIEW,
	    self::TYPE_APP,
	    self::TYPE_LOCALE,
	    self::TYPE_BEAN,
	    self::TYPE_STDCLASS
	);
	
	public function __construct($className, $app = null) {
		
		if ($app) { $className = substr ($className, strlen($app->getNameSpace())); }
		
		$this->fullName  = $className;
		
		$this->namespace = explode (self::NAMESPACE_SEPARATOR, $className);
		$this->className = array_pop($this->namespace);
		
		$full = preg_split('/(?=[A-Z])/', $this->className, 2, PREG_SPLIT_NO_EMPTY);
		$type = in_array(end($full), $this->types)? array_pop($full) : self::TYPE_STDCLASS;
		$name = implode('', $full);
		
		$this->className = $name;
		$this->type      = $type;
		
	}
	
	public function getNameSpace () {
		return $this->namespace;
	}
	
	public function getType() {
		return $this->type;
	}
	
	public function getClassName($complete = false) {
		if ($complete) {
			return $this->className . $this->type;
		}
		else {
			return $this->className;
		}
	}
	
	/**
	 * Locates a file and includes it if possible. Otherwise it will just silently
	 * return false. 
	 * 
	 * This allows applications to either handle errors with includes without issue
	 * or to ignore the potential error situation that could arise.
	 * 
	 * @param string $file
	 * @return boolean|mixed
	 */
	public static function includeIfPossible($file) {
		if (file_exists($file)) { return include $file; }
		else { return false; }
	}
	
}
