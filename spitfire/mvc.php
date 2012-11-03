<?php

abstract class controller
{
	
	//abstract public function index ($object, $params);
	//abstract public function detail ($object, $params);
	
	public $view;
	public $model;
	
	public function __construct() {
		$this->memcached = new _SF_Memcached();
		$this->call      = new _SF_Invoke();
		$this->post      = new _SF_InputSanitizer($_POST);
		$this->get       = new _SF_InputSanitizer($_GET);
		
		$this->view      = SpitFire::$view;
		$this->model     = SpitFire::$model;
	}
	
	public function __call($method, $args) {
		return $this->detail($method, $args);
		//else throw new BadMethodCallException('Invalid argument count. Requires two args.', 0);
	}
	
}

class view
{
	private $file = '';
	private $data = Array();
	
	private $render_layout = true;
	private $layout = "bin/views/layout.php";
	
	const default_view = 'bin/views/default.php';
	
	public function __construct($controller, $action) {
		if     ( file_exists("bin/views/$controller/$action.php"))
			$this->file = "bin/views/$controller/$action.php";
		elseif ( file_exists("bin/views/$controller.php"))
			$this->file = "bin/views/$controller.php";
		else
			$this->file = self::default_view;
	}
	
	public function set($key, $value) {
		//echo $key;
		$this->data[$key] = $value;
	}

	public function render () {
		ob_start();
		foreach ($this->data as $data_var => $data_content) {
			$$data_var = $data_content;
		}
		include $this->file;
		$content_for_layout = ob_get_clean();
		
		if ($this->render_layout && file_exists($this->layout) ) include ($this->layout);
		else echo $content_for_layout;
	}
	
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
