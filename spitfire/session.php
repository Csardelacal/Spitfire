<?php

class session
{

	private $secure  = false;

	public function set($key, $value) {

		if (!session_id()) $this->start();
		$_SESSION[$key] = $value;

	}

	public function get($key) {

		if (!session_id()) $this->start();
		return isset($_SESSION[$key])? $_SESSION[$key] : null;

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
			return $started;
		}

		elseif (!is_dir(session_save_path())) 

			if (mkdir(session_save_path(), 0777, true)) {
				$started = session_start();
				$this->isSafe();
				return $started;
			}
			else throw new fileNotFoundException("Session path ".session_save_path()." couldn't be created");

		else throw new filePermissionsException("Session path is not writable");
	}
	
	public function destroy() {
		if (!session_id()) $this->start();
		return session_destroy();
	}

}