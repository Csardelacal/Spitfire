<?php

class View extends _SF_MVC
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