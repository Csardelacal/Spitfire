<?php

namespace M3W\Admin;

use Controller;
use publicException;
use CoffeeBean;

class homeController extends Controller
{
	function index() {
		$this->view->set('beans', $this->app->getBeans());
		
	}
	
	function lst($bean) {
		if (!in_array($bean, $this->app->getBeans())) throw new publicException('Not found', 404);
		$b = CoffeeBean::getBean($bean);
		
		$q = db()->table($b->model)->getAll();
		if (isset($_GET['page'])) $q->setPage($_GET['page']);
		$this->view->set('records', $q->fetchAll());
		$this->view->set('bean', $bean);
	}
	
	function create($bean) {
		if (in_array($bean, $this->app->getBeans())) {
			$beanClass = $bean . 'Bean';
			$this->view->set('bean', new $beanClass());
		}
		else throw new publicException('Not found', 404);
	}
}