<?php

class session
{

	private $started = false;
	private $secure  = false;

	public function set($key, $value) {

		if (!$this->started) $this->start();
		$_SESSION[$key] = $value;

	}

	public function get($key) {

		if (!$this->started) $this->start();
		return $_SESSION[$key];

	}

	public function lock($userdata) {

		$user = Array();
		$user['ip']       = $_SERVER['REMOTE_ADDR'];
		$user['userdata'] = $userdata;
		$user['secure']   = true;

		$this->set('_SF_Auth', $user);

	}

	public function isSafe() {

		$user = $this->get('_SF_Auth');
		if ($user) {
			$user['secure'] = $user['secure'] && ($user['ip'] == $_SERVER['REMOTE_ADDR']);

			$this->set('_SF_Auth', $user);
			return $user['secure'];
		} 
		else return false;

	}

	public function getUser() {

		$user = $this->get('_SF_Auth');
		return $user['userdata'];
		
	}

	public function start() {
		if ( is_writable(session_save_path()) ) {
			$started = session_start();
			$this->isSafe();
			if ($started) $this->started = true;
			return $started;
		}

		elseif (!is_dir(session_save_path())) 

			if (mkdir(session_save_path(), 0777, true)) {
				$started = session_start();
				if ($started) $this->started = true;
				return $started;
			}
			else throw new fileNotFoundException("Session path couldn't be created");

		else throw new filePermissionsException("Session path is not writable");
	}

}