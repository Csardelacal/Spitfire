<?php

namespace M3W\Admin;

use Controller;
use publicException;
use CoffeeBean;
use session;

class homeController extends Controller
{
	private $session;
	
	function onload() {
		
		$this->session = new session();
		if (!$this->session->isSafe()) {
			die(header('location: ' . $this->app->url('/auth/') ));
		}
	}


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
			$this->view->set('bean', $bean);
		}
		else throw new publicException('Not found', 404);
	}
	
	function edit($bean, $id) {
		if (in_array($bean, $this->app->getBeans())) {
			$this->view->set('bean', $bean);
			$this->view->set('record', db()->table(CoffeeBean::getBean($bean)->model)->get('id', $id)->fetch());
		}
		else throw new publicException('Not found', 404);
	}
	
	function insert($bean) {
		if (in_array($bean, $this->app->getBeans())) {
			$r = CoffeeBean::getBean($bean)->makeDBRecord();
			$r->store();
			die(header('location: ' . $this->app->url('/edit/' . $bean . '/' . $r->id) ));
		}
		else throw new publicException('Not found', 404);
	}
	
	function update($bean, $id) {
		if (in_array($bean, $this->app->getBeans())) {
			$data = db()->table(CoffeeBean::getBean($bean)->model)->get('id', $id)->fetch();
			$r = CoffeeBean::getBean($bean)->updateDBRecord($data);
			$r->store();
			die(header('location: ' . $this->app->url('/edit/' . $bean . '/' . $r->id) ));
		}
		else throw new publicException('Not found', 404);
	}
}