<?php namespace spitfire\io\session;

use spitfire\cache\MemcachedAdapter;

class MemcachedSessionHandler extends SessionHandler
{
	/**
	 * 
	 * @var MemcachedAdapter
	 */
	private $memcached;
	
	public function __construct(MemcachedAdapter$memcached, $timeout = null) {
		$this->memcached = $memcached;
		$this->memcached->setTimeout($timeout);
		parent::__construct($timeout);
	}
	
	public function close() {
		return true;
	}
	
	public function destroy($id) {
		$key = sprintf('sf_sess_%s', $id);
		$this->memcached->delete($key);
		
		return true;
	}
	
	public function gc($maxlifetime) {
		return true;
	}
	
	public function open($savePath, $sessionName) {
		return true;
	}
	
	public function read($id) {
		$key = sprintf('sf_sess_%s', $id);
		return (string)$this->memcached->get($key);
	}
	
	public function write($id, $data) {
		$key = sprintf('sf_sess_%s', $id);
		return $this->memcached->set($key, $data);
	}

}
