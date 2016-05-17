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
				array($this, 'open'),
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
		
	abstract public function open($savePath, $sessionName);
	abstract public function close();
	abstract public function read($id);
	abstract public function write($id, $data);
	abstract public function destroy($id);
	abstract public function gc($maxlifetime);
	
}

