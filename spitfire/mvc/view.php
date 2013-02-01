<?php

namespace spitfire;

use _SF_MVC;

class View extends _SF_MVC
{
	private $file = '';
	private $data = Array();
	
	private $render_layout = true;
	private $layout = "bin/views/layout.php";
	
	const default_view = 'bin/views/default.php';
	
	/**
	 * 
	 * @param URL $url
	 */
	public function __construct() {
		
		$url        = $this->current_url;
		$controller = implode(DIRECTORY_SEPARATOR, $url->getController());
		$action     = $url->getAction();
		$extension  = $url->getExtension();
		
		if     ( file_exists("bin/views/$controller/$action.$extension"))
			$this->file = "bin/views/$controller/$action.$extension";
		elseif ( file_exists("bin/views/$controller.$extension"))
			$this->file = "bin/views/$controller.$extension";
		else
			$this->file = self::default_view;
	}
	
	/**
	 * Defines a variable inside the view.
	 * @param String $key
	 * @param mixed $value
	 */
	public function set($key, $value) {
		//echo $key;
		$this->data[$key] = $value;
	}
	
	public function setFile ($fileName) {
		if (file_exists($filename)) $this->file($fileName);
		else throw new fileNotFoundException('File ' . $fileName . 'not found. View can\'t use it');
	}


	public function element($file) {
		$filename = 'bin/views/elements/' . $file . '.php';
		if (!file_exists($filename)) throw new privateException('Element ' . $file . ' missing');
		return new _SF_ViewElement($filename, $this->data);
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