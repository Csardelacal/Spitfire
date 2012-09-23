<?php

class homeController extends controller
{

	public function index ($object = '', $params = '') {
		set('FW_NAME', 'Spitfire');
		set('controller', __CLASS__ . '&gt' . $object . '&gt' . $params);
	}

	public function detail($object, $params) {
		//DO nothing
	}

	public function save ($object, $params) {
		set('FW_NAME', 'Spitfire');
		set('name', $this->post->name->value());
		set('age',  $this->post->age->toInt());
		set('pass', $this->post->pass->toPassword());
	}

	
}