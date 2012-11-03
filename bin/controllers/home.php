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
		$this->view->set('FW_NAME', 'Spitfire');
		$this->view->set('name', $this->post->name->value());
		$this->view->set('age',  $this->post->age->toInt());
		$this->view->set('pass', $this->post->pass->toPassword());
	}
	
}