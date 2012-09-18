<?php

abstract class controller
{
	
	abstract public function index ($object, $params);
	abstract public function detail ($object, $params);
	
	public function __construct() {
		$this->memcached = new _SF_Memcached();
		$this->call      = new _SF_Invoke();
		$this->post      = new _SF_InputSanitizer($_POST);
		$this->get       = new _SF_InputSanitizer($_GET);
	}
	
	public function __call($method, $args) {
		if ( count($args) == 2 ) return $this->detail($method, $args[1]);
		else throw new BadMethodCallException('Invalid argument count. Requires two args.', 0);
	}
	
}

class view
{
	private $file = '';
	
	const default_view = 'bin/views/default.php';
	
	public function __construct($controller, $action) {
		if     ( file_exists("bin/views/$controller/$action.php")) $this->file = "bin/views/$controller/$action.php";
		elseif ( file_exists("bin/views/$controller.php"))         $this->file = "bin/views/$controller.php";
		else   $this->file = self::default_view;
	}
	
	public function render () {
		foreach ($GLOBALS['_SF_ViewData'] as $data_var => $data_content) {
			$$data_var = $data_content;
		}
		include $this->file;
	}
	
}

function set($key, $value) {
	$GLOBALS['_SF_ViewData'][$key] = $value;
}

/**
 * This class will autoinclude functions the application needs to run and return
 * the result
 */
class _SF_Invoke
{
	public function __call($function_name, $arguments) {
		if ( !is_callable($function_name) ) {
			$file = 'bin/functions/'.$function_name.'.php';
			if (file_exists($file)) include $file;
			else throw new privateException('Undefined function: '. $function_name);
		}
		
		return call_user_func_array($function_name, $arguments);
		
	}
}

function _autoload($class) {
	@list($m, $n) = explode('_', $class, 2); //Yes, this can produce errors but that's none of our business
	switch ($m) {
		//Imports controllers
		case 'controller':
			if ( file_exists($f = 'bin/controllers/'. $n . '.php') ) include $f;
			else throw new publicException('Controller '. $n . ' not found.', 404);
			break;
		//Imports HTML node inherited classes
		case 'html':
			if ( file_exists($f = 'bin/html/'. $n . '.php') ) include $f;
			else throw new fileNotFoundException('HTML class '. $class . ' not found.', 0);
			break;
		//Imports all the rest
		default:
			if ( file_exists($f = 'bin/classes/'. $class . '.php') ) include $f;
			else throw new fileNotFoundException('Function class '. $class . ' not found.', 0);
			break;
	}
}
spl_autoload_register('_autoload');