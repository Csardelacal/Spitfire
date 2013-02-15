<?php

namespace M3W\Admin;

use Controller;

class homeController extends Controller
{
	function index() {
		$this->view->set('beans', $this->app->getBeans());
		
	}
	
	function lst($bean) {
		if (in_array($bean, $this->app->getBeans())) {
			$beanClass = $bean . 'Bean';
			$this->view->set('bean', new $beanClass());
		}
		else throw new \publicException('Not found', 404);
	}
}