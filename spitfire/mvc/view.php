<?php

namespace spitfire;

use _SF_MVC;
use \_SF_ViewElement;
use spitfire\registry\JSRegistry;
use spitfire\registry\CSSRegistry;

class View extends _SF_MVC
{
	private $file = '';
	private $data = Array();
	
	private $js;
	private $css;
	
	private $render_layout = true;
	private $layout;
	
	const default_view = 'default.php';
	
	/**
	 * 
	 * @param App $app
	 */
	public function __construct($app) {
		
		parent::__construct($app);
		
		#Create registries
		$this->js  = new JSRegistry();
		$this->css = new CSSRegistry();
		
		/*
		 * Set default files. This includes the view's file, layout and
		 * the basedir for elements.
		 */
		
		$basedir    = $app->getTemplateDirectory();
		
		$url        = $this->current_url;
		$controller = implode(DIRECTORY_SEPARATOR, $url->getControllerURI());
		$action     = $url->getAction();
		$extension  = $url->getExtension();
		
		spitfire()->getRequest()->getHeaders()->contentType($extension);
		
		
		if     ( file_exists("$basedir$controller/$action.$extension"))
			$this->file = "$basedir$controller/$action.$extension";
		elseif ( file_exists("$basedir$controller.$extension"))
			$this->file = "$basedir$controller.$extension";
		else
			$this->file = $basedir . self::default_view;
		
		
		if     ( file_exists("{$basedir}layout.$extension"))
			$this->layout = "{$basedir}layout.$extension";
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
		if (file_exists($fileName)) $this->file($fileName);
		else throw new fileNotFoundException('File ' . $fileName . 'not found. View can\'t use it');
	}


	public function element($file) {
		$filename = $this->getApp()->getTemplateDirectory() . 'elements/' . $file . '.php';
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
	
	public function css($add = null) {
		if ($add) $this->css->add ($add);
		else return $this->css;
	}
	
	public function js($add = null) {
		if ($add) $this->js->add ($add);
		else return $this->js;
	}
	
}