<?php

namespace M3W\Admin;

use Controller;
use _SF_InputSanitizer;
use session;

class authController extends Controller
{
	/** @var session Session info */
	private $session;
	
	function onload() {
		
		$this->session = new session();
		
	}
	
	public function index() {
		$model = $this->app->getUserModel();
		if (!db()->table($model)->getAll()->count()) {
			$user = db()->table($model)->newRecord();
			$user->username = 'admin';
			$user->email = 'admin@example.com';
			$user->admin = true;
			$password = new _SF_InputSanitizer('admin');
			$user->password = $password->toPassword();
			$user->store();
		}
	}
	
	public function login() {
		$user = db()->table($this->app->getUserModel())
			->get('admin', true)
			->group()
				->addRestriction('username', $this->post->username->value())
				->addRestriction('email', $this->post->username->value())
			->endGroup()
			->addRestriction('password', $this->post->password->toPassword())
			->fetch();
		
		
		if ($user->id) {
			$this->session->lock($user->id);
			header ('location: '. $this->app->url('/'));
		}
		else header ('location: '. $this->app->url('/auth'));
	}
	
	
	public function logout() {
		$this->session->destroy();
		header('location: ' . $this->app->url('/') );
	}
}