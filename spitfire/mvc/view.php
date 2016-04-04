<?php namespace spitfire;

use spitfire\exceptions\PrivateException;
use spitfire\exceptions\FileNotFoundException;
use spitfire\mvc\MVC;
use spitfire\core\Context;
use \_SF_ViewElement;
use spitfire\registry\JSRegistry;
use spitfire\registry\CSSRegistry;

class View extends MVC
{
	private $file = '';
	private $data = Array();
	
	private $js;
	private $css;
	
	private $render_template = true;
	private $render_layout = true;
	private $layout;
	private $extension;
	
	const default_view = 'default.php';
	
	/**
	 * Creates a new view. The view allows to present the data your application 
	 * manages in a consistent way and manage and locate the templates the app
	 * needs.
	 * 
	 * @param \spitfire\Context $context
	 */
	public function __construct(Context$context) {
		
		parent::__construct($context);
		
		#Get the answer format
		$this->extension = $context->request->getPath()->getFormat();
		#Create registries
		$this->js  = new JSRegistry();
		$this->css = new CSSRegistry();
		
		#Initialize the files
		$this->getFiles();
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
		
		
		if ( file_exists("$basedir$controller/$action$extension.php"))
			$this->file = "$basedir$controller/$action$extension.php";
		else
			$this->file = "$basedir$controller$extension.php";
		
		
		if ( file_exists("{$basedir}layout$extension.php"))
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
	
	/**
	 * Sets the file to be used by the template system. Please note that it can
	 * accept either the full patch to the file (like bin/templates/controller/action.php)
	 * or just the path relative to the template directory.
	 * 
	 * Using the relative Path to the template directory would be a recommended 
	 * practice in order to reduce the need to change your coding when changing
	 * your directory structure.
	 * 
	 * @param string $fileName
	 * @throws FileNotFoundException
	 */
	public function setFile ($fileName) {
		
		if (!file_exists($fileName)) { $fileName = $this->app->getTemplateDirectory() . $fileName; }
		
		if (file_exists($fileName)) { $this->file = $fileName; }
		else { throw new FileNotFoundException('File ' . $fileName . 'not found. View can\'t use it'); }
	}


	public function element($file) {
		$filename = $this->app->getTemplateDirectory() . 'elements/' . $file . '.php';
		if (!file_exists($filename)) throw new PrivateException('Element ' . $file . ' missing');
		return new _SF_ViewElement($filename, $this->data);
	}
	
	public function setRenderTemplate($set) {
		$this->render_template = $set;
	}

	public function render () {
		#If the template is not to be rendered at all. Use this.
		if (!$this->render_template) { echo $this->data['_SF_DEBUG_OUTPUT']; return; }
		
		#Consider that a missing template file that should be rendered is an error
		if (!file_exists($this->file)) { throw new PrivateException('Missing template file for ' . get_class($this->controller) . '::' . $this->action); }
		
		ob_start();
		foreach ($this->data as $data_var => $data_content) {
			$$data_var = $data_content;
		}
		include $this->file;
		$content_for_layout = ob_get_clean();
		
		if ($this->render_layout && file_exists($this->layout) ) { include ($this->layout); }
		else { echo $content_for_layout; }
	}
	
	public function css($add = null) {
		if ($add) { $this->css->add ($add); }
		else      { return $this->css; }
	}
	
	public function js($add = null) {
		if ($add) $this->js->add ($add);
		else return $this->js;
	}
	
}