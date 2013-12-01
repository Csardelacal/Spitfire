<?php

namespace M3W\admin;

use Controller;
use publicException;
use CoffeeBean;
use session;
use URL;

class homeController extends Controller
{
	private $session;
	
	function onload() {
		
		$this->session = new session();
		if (!$this->session->isSafe()) {
			die(header('location: ' . $this->app->url('/auth/') ));
		}
		
		$this->view->set('beans', $this->app->getBeans());
		$this->view->set('bean',  '');
	}


	function index() {
		
	}
	
	function lst($bean) {
		
		$current_bean = null;
		
		foreach ($beans = $this->getApp()->getBeans() as $b) {
			if ($b->getName() == $bean) $current_bean = $b;
		}
		
		if (!$current_bean) throw new publicException('Not found', 404);
		
		$q = $current_bean->getTable()->getAll();
		
		if ($this->get->search->toBool()) {
			$q->addRestriction(null, '%' . $this->get->search->value() . '%', 'LIKE');
		}
		
		if ($this->get->order->toBool()) {
			$q->setOrder($this->get->order->field->value(), $this->get->order->method->value());
		}
		
		$p = new \Pagination($q);
		
		$this->view->set('records', $q->fetchAll());
		$this->view->set('paging',  $p);
		$this->view->set('bean', $current_bean);
		$this->view->set('search_enabled', true);
		
		if ($this->get->deleted->value() === 'ok') {
			$this->view->set('message', 'Record deleted successfully');
			$this->view->set('message_type', 'Success');
		}
	}
	
	function create($bean) {
		
		$current_bean = null;
		
		foreach ($beans = $this->getApp()->getBeans() as $b) {
			if ($b->getName() == $bean) $current_bean = $b;
		}
		
		/* @var $current_bean \CoffeeBean  */
		
		if (!$current_bean) throw new publicException('Not found', 404);
		$record = $current_bean->getTable()->newRecord();
		$current_bean->setDBRecord($record);
		
		
		$current_bean->readPost();
		
		try {
			validate($current_bean);
			$current_bean->updateDBRecord()->store();
			$this->response->getHeaders()->redirect(new URL($this->app, 'edit', $current_bean->getName(), implode(':', $record->getPrimaryData())));
			$this->response->setBody('Stored');
			return;
		} catch (\spitfire\io\beans\UnSubmittedException $ex) {
			$errors = Array();
		} catch (\ValidationException $ex) {
			$errors = $ex->getResult()->getErrors();
		}
		
		$this->view->set('errors', $errors);
		$this->view->set('record', $record);
		$this->view->set('bean', $current_bean);
	}
	
	function edit($bean, $id) {
		
		$current_bean = null;
		
		foreach ($beans = $this->getApp()->getBeans() as $b) {
			if ($b->getName() == $bean) $current_bean = $b;
		}
		
		/* @var $current_bean \CoffeeBean  */
		
		if (!$current_bean) throw new publicException('Not found', 404);
		
		if ($id)
			$record = $current_bean->getTable()->getById($id);
		
		$current_bean->readPost();
		$current_bean->setDBRecord($record);
		
		try {
			validate($current_bean);
			$current_bean->updateDBRecord()->store();
			$errors = Array();
		} catch (\spitfire\io\beans\UnSubmittedException $ex) {
			$errors = Array();
		} catch (\ValidationException $ex) {
			$errors = $ex->getResult()->getErrors();
		}
		
		$this->view->set('errors', $errors);
		$this->view->set('record', $record);
		$this->view->set('bean', $current_bean);
	}
	
	public function delete($bean, $id) {
		
		$current_bean = null;

		foreach ($beans = $this->getApp()->getBeans() as $b) {
			if ($b->getName() == $bean) { $current_bean = $b; }
		}

		if (!$current_bean) { throw new publicException('Not found', 404); }
		
		$record = $current_bean->getTable()->getById($id);

		if ($this->get->confirmed->toBool()) {
			$record->delete();
			$this->response->getHeaders()->redirect(new URL($this->app, 'lst', $current_bean->getName(), Array('deleted' => 'ok')));
			$this->response->setBody('Stored');
			return;
		} else {
			$this->view->set('bean',   $current_bean);
			$this->view->set('record', $record);
		}
		
	}
}