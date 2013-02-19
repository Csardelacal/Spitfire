<?php

namespace M3W\Admin;

use Controller;
use publicException;
use CoffeeBean;
use session;

class authController extends Controller
{
	private $session;
	
	function onload() {
		
		$this->session = new session();
		
	}
	
	public function index() {
		
	}
	
	public function login() {
		$user = db()->table($this->app->getUserModel())
			->get('username', $this->post->username->value())
			->addRestriction('password', $this->post->password->toPassword())
			->fetch();
		
		
		if ($user->id) {
			$this->session->lock($user->id);
			header ('location: '. $this->app->url('/'));
		}
		else header ('location: '. $this->app->url('/auth'));
	}
}