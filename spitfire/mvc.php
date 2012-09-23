<?php

abstract class controller
{
	
	//abstract public function index ($object, $params);
	//abstract public function detail ($object, $params);
	
	public function __construct() {
		$this->memcached = new _SF_Memcached();
		$this->call      = new _SF_Invoke();
		$this->post      = new _SF_InputSanitizer($_POST);
		$this->get       = new _SF_InputSanitizer($_GET);
	}
	
	public function __call($method, $args) {
		return $this->detail($method, $args);
		//else throw new BadMethodCallException('Invalid argument count. Requires two args.', 0);
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
