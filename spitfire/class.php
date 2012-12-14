<?php

class _SF_Class
{
	const TYPE_CONTROLLER = 'Controller';
	const TYPE_COMPONENT  = 'Component';
	const TYPE_MODEL      = 'Model';
	const TYPE_STDCLASS   = '';
	
	private $type      = '';
	private $namespace = Array();
	private $className = '';
	private $fullName  = '';
	
	private $types = Array(
	    self::TYPE_CONTROLLER,
	    self::TYPE_COMPONENT,
	    self::TYPE_MODEL,
	    self::TYPE_STDCLASS
	);
	
	public function __construct($className) {
		$this->fullName  = $className;
		
		$this->namespace = explode ('_', $className);
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