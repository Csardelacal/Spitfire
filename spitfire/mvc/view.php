<?php

namespace spitfire;

use privateException;
use spitfire\MVC;
use \_SF_ViewElement;
use spitfire\registry\JSRegistry;
use spitfire\registry\CSSRegistry;

class View extends MVC
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
	public function __construct(Context$intent) {
		
		parent::__construct($intent);
		
		#Create registries
		$this->js  = new JSRegistry();
		$this->css = new CSSRegistry();
	}
	
	public function getFiles() {
		/*
		 * Set default files. This includes the view's file, layout and
		 * the basedir for elements.
		 */
		
		$basedir    = $this->app->getTemplateDirectory();
		
		$controller = strtolower(implode('\\', $this->app->getControllerURI($this->controller)));
		$action     = $this->action;
		$extension  = $this->extension === 'php'? '' : '.' . $this->extension;
		
		spitfire()->getRequest()->getResponse()->getHeaders()->contentType($extension);
		
		
		if     ( file_exists("$basedir$controller/$action$extension.php"))
			$this->file = "$basedir$controller/$action$extension.php";
		elseif ( file_exists("$basedir$controller$extension.php"))
			$this->file = "$basedir$controller$extension.php";
		else
			$this->file = $basedir . self::default_view;
		
		
		if     ( file_exists("{$basedir}layout$extension.php"))
			$this->layout = "{$basedir}layout$extension.php";
	}
	
	/**
	 * Defines a variable inside the view.
	 * @param String $key
	 * @param mixed $value
	 */
	public function set($key, $value) {
		//echo $key;
		$this->data[$key] = $value;
		return $this;
	}
	
	public function setFile ($fileName) {
		if (file_exists($fileName)) $this->file = $fileName;
		else throw new fileNotFoundException('File ' . $fileName . 'not found. View can\'t use it');
	}


	public function element($file) {
		$filename = $this->getApp()->getTemplateDirectory() . 'elements/' . $file . '.php';
		if (!file_exists($filename)) throw new privateException('Element ' . $file . ' missing');
		return new _SF_ViewElement($filename, $this->data);
	}

	public function render () {
		$this->getFiles();
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