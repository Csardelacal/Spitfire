<?php namespace spitfire\core\router;

use spitfire\core\Path;
use Strings;

class RouteParser
{
	private $parsers = Array();
	
	protected static $instance;
	
	protected function __construct() {
		self::$instance = $this;
	}

	/**
	 * Reads the current path the user has selected and tries to detect
	 * Controllers, actions and objects from it.
	 * 
	 * [NOTICE] readPath does not guarantee safe input, you will have to
	 * manually check whether the input it received is valid.
	 * 
	 * @throws \publicException In case a controller with no callable action
	 *            has been found.
	 */
	public function readPath() {
		$path = array_filter(explode('/', $this->path));
		$_ret = new Path(null, null, null, null);
		
		list($last, $extension) = Strings::splitExtension(array_pop($path), 'php');
		array_push($path, $last);
		
		$handlers = $this->getHandlers();
		foreach ($handlers as $handler) {
			if ($handler($_ret, reset($path))) {
				array_shift($path);
			}
		}
		
		$_ret->setObject($path);
		$_ret->setExtension($extension);
		
		return $_ret;
	}
	
	public function addHandler($parser) {
		$this->parsers[] = $parser;
	}
	
	public function getHandlers() {
		return $this->parsers;
	}
	
	/**
	 * 
	 * 
	 * @return RouteParser
	 */
	public static function getInstance() {
		return self::$instance? self::$instance : new self();
	}
	
}