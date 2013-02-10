<?php


class adminController extends Controller
{
	private $config;
	private $ctx;
	
	public function __construct() {
		parent::__construct();
		$this->ctx = ComponentManager::get('M3W', 'admin');
		$this->config = $this->ctx->getConfig();
	}
	public function index() {
		echo "Welcome to the admin panel. \nRunning from: {$this->ctx->getDir()}\nThis are the models loaded into this:";
		
		$models = $this->config->getModels();
		$model = db()->table($models[0])->getModel();
		print_r($model);
	}
}