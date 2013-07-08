<?php

namespace spitfire;

use \privateException;
use \Memcached;

/**
 * Memcached interface. This allows to retrieve cached data easily and increases
 * performance.
 * 
 * Instead of automatically pushing data onto the cache server, this class keeps
 * a list of the keys and writes them on destruction. Please note that this makes
 * it a recommended behavior to use just one instance of the class, to allow 
 * overriding and save memory.
 * 
 */
class MemcachedAdapter
{
	const DEFAULT_TIMEOUT = 14400; //4Hours
	
	public static $instance = null;
	
	protected $timeout    = self::DEFAULT_TIMEOUT;
	protected $connection = false;
	
	/**
	 * Creates a connection to the memcached servers.
	 * @uses $memcached_servers List of servers used by this function
	 * @return memcache 
	 */
	public function connect () {
		$memcached_servers = environment::get('memcached_servers');
		
		
		if (!environment::get('memcached_enabled')) return;
		if (!class_exists('\Memcached') ) throw new privateException('Memcached is enabled but not installed');
		
		$this->connection = new Memcached();
		foreach ($memcached_servers as $server) $this->connection->addServer($server, environment::get('memcached_port'));
		return $this->connection;
		
	}
	
	/**
	 * A MC instance will just check if MC Connection exists by calling 
	 * @uses memcached::getConnection();
	 */
	protected function __construct(){
		$this->connect();
	}
	
	public function setKey($key, $value) {
		if ($this->connection) {
			return $this->connection->set($key, $value, $this->timeout);
		}
	}
	
	public function getKey($key) {
		if ($this->connection) {
			return $this->connection->get($key);
		}
	}
	
	public function setTimeout($timeout) {
		$this->timeout = $timeout;
	}
	
	public function __get($var) {
		return $this->getKey($var);
	}
	
	/** 
	 * Simulates the use of memcached like a variable 
	 * i.e. <code>$mc->foo = "bar"</code>
	 * @param string $var Key to read
	 * @param mixed  $val Data to write to the key
	 */
	public function __set($var, $val) {
		return $this->$var = $val;
	}
	
	public function __destruct() {
		$data = get_object_vars($this);
		
		unset($data['timeout']);
		unset($data['connection']);
		
		foreach ($data as $key => $value) $this->setKey($key, $value);
	}
	
	public static function getInstance() {
		
		if (self::$instance === null) self::$instance = new \spitfire\MemcachedAdapter();
		return self::$instance;
	}
}