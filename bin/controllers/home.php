<?php

class homeController extends controller
{

	public function index ($object = '', $params = '') {
		$this->view->set('FW_NAME', 'SpitfirePHP');
		$this->view->set('controller', __CLASS__ . '&gt' . $object . '&gt' . $params);
	}

	public function detail($object, $params) {
		//DO nothing
	}

	public function save ($object, $params) {
		$this->view->set('FW_NAME', 'Spitfire - ' . memory_get_peak_usage()/1024);
		$this->view->set('name', $this->post->name->value());
		$this->view->set('age',  $this->post->age->toInt());
		$this->view->set('pass', $this->post->pass->toPassword());
		
		$this->view->set('test', 
			$this->model->content->get('page', 'index')
			->addRestriction(new _SF_Restriction('content', 'This is a sample article.  All of this text you can change in the script admin control panel.Hello world'))
			->fetch());
	}
	
}