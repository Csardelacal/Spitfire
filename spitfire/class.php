<?php

namespace spitfire;

class ClassInfo
{
	const TYPE_CONTROLLER = 'Controller';
	const TYPE_COMPONENT  = 'Component';
	const TYPE_MODEL      = 'Model';
	const TYPE_VIEW       = 'View';
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
	    self::TYPE_VIEW,
	    self::TYPE_BEAN,
	    self::TYPE_STDCLASS
	);
	
	public function __construct($className) {
		$this->fullName  = $className;
		
		$this->namespace = explode (self::NAMESPACE_SEPARATOR, $className);
		$this->className = array_pop($this->namespace);
		
		foreach($this->types as $type) {
			if ( substr($this->className, 0 - strlen($type) ) == $type) {
				$this->type      = $type;
				$this->className = substr($this->className, 0, strlen($this->className) - strlen($type) );
			}
			
		}
		
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
	
}