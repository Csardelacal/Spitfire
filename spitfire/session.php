<?php

class session
{

	private $secure  = false;

	public function set($key, $value, $app = null) {
		if ($app === null) {$app = current_context()->app;}
		/* @var $app App */
		$namespace = ($app->getNameSpace())? $app->getNameSpace() : '*';

		if (!session_id()) $this->start();
		$_SESSION[$namespace][$key] = $value;

	}

	public function get($key, $app = null) {
		if ($app === null) {$app = current_context()->app;}
		$namespace = ($app->getNameSpace())? $app->getNameSpace() : '*';

		if (!session_id()) $this->start();
		return isset($_SESSION[$namespace][$key])? $_SESSION[$namespace][$key] : null;

	}

	public function lock($userdata, App$app = null) {
		
		$user = Array();
		$user['ip']       = $_SERVER['REMOTE_ADDR'];
		$user['userdata'] = $userdata;
		$user['secure']   = true;

		$this->set('_SF_Auth', $user, $app);

	}

	public function isSafe(App$app = null) {

		$user = $this->get('_SF_Auth', $app);
		if ($user) {
			$user['secure'] = $user['secure'] && ($user['ip'] == $_SERVER['REMOTE_ADDR']);

			$this->set('_SF_Auth', $user, $app);
			return $user['secure'];
		} 
		else return false;

	}

	public function getUser(App$app = null) {

		$user = $this->get('_SF_Auth', $app);
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