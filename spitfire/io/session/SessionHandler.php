<?php namespace spitfire\io\session;

abstract class SessionHandler implements SessionHandlerInterface
{
	
	private $timeout = 1200;
	
	public function __construct($timeout) {
		$this->timeout = $timeout;
	}
	
	public function attach() {
		if ($this instanceof \SessionHandlerInterface) {
			session_set_save_handler($this);
		} 
		else {
			session_set_save_handler(
				array($this, 'start'),
				array($this, 'close'),
				array($this, 'read'),
				array($this, 'write'),
				array($this, 'destroy'),
				array($this, 'gc')
			);
		}
	}
	
	public function getTimeout() {
		return $this->timeout;
	}
	
	public function setTimeout($timeout) {
		$this->timeout = $timeout;
		return $this;
	}
	
	public function start($savePath, $sessionName) {
		
		/**
		 * Open the session. The start method is kinda special, since we need to 
		 * set the cookies right after opening it. So we register this hook that 
		 * will open the session and then send the cookies.
		 */
		$this->open($savePath, $sessionName);
		
		/*
		 * This is a fallback mechanism that allows dynamic extension of sessions,
		 * otherwise a twenty minute session would end after 20 minutes even 
		 * if the user was actively using it.
		 * 
		 * Read on: http://php.net/manual/en/function.session-set-cookie-params.php
		 */
		$lifetime = 2592000;
		setcookie(session_name(), session_id(), time() + $lifetime, '/');
	}
		
	abstract public function open($savePath, $sessionName);
	abstract public function close();
	abstract public function read($id);
	abstract public function write($id, $data);
	abstract public function destroy($id);
	abstract public function gc($maxlifetime);
	
}

