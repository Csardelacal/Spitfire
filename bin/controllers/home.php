<?php

class controller_home extends controller
{

	public function index ($object, $params) {
		set('FW_NAME', 'Spitfire');
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