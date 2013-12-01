<?php

namespace M3W\Admin;

use URL;
use Controller;
use spitfire\InputSanitizer;
use session;
use spitfire\Request;

/**
 * This controller handles user and session creation and destruction. This allows
 * all the other controllers to simply require a valid login.
 * 
 * @property-read adminApp $app The app this controller is running under
 */
class authController extends Controller
{
	/** @var session Session info */
	private $session;
	
	function onload() {
		
		$this->session = new session();
		
	}
	
	public function index() {
		$table = $this->app->getUserTable();
		if (!$table->getAll()->count()) {
			$user = $table->newRecord();
			$user->username = 'admin';
			$user->email = 'admin@example.com';
			$user->admin = true;
			$password = new InputSanitizer('admin');
			$user->password = $password->toPassword();
			$user->store();
		}
	}
	
	public function login() {
		$user = $this->app->getUserTable()
			->get('admin', true)
			->group()
				->addRestriction('username', $this->post->username->value())
				->addRestriction('email', $this->post->username->value())
			->endGroup()
			->addRestriction('password', $this->post->password->toPassword())
			->fetch();
		
		
		if ($user->_id) {
			$this->session->lock($user->_id);
			$this->response->getHeaders()->redirect(new URL($this->app));
		}
		else $this->response->getHeaders()->redirect(new URL($this->app, 'auth'));
	}
	
	
	public function logout() {
		$this->session->destroy();
		$this->response->getHeaders()->redirect(new URL($this->app));
	}
}