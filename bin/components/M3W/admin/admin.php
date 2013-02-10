<?php

use M3W\adminComponent;

class adminController extends Controller
{
	private $config;
	private $ctx;
	
	public function __construct() {
		parent::__construct();
		$this->config = adminComponent::getConfig();
		$this->ctx = new adminComponent();
	}
	public function index() {
		echo "Welcome to the admin panel. \nRunning from: {$this->ctx->getDir()}\nThis are the models loaded into this:";
		
		$models = $this->config->getModels();
		$model = db()->table($models[0])->getModel();
		print_r($model);
	}
}