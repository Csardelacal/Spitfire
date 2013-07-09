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
 * Yes, this class uses __get to retrieve data, for two obvious reasons.
 * <ul>
 * <li>It's really comfortable to retrieve the data like <code>$mc->cached_key</code></li>
 * <li>Performance. Magic gets are still way faster than reading from memcached</li>
 * </ul>
 * 
 */
class MemcachedAdapter
{
	/**
	 * Defines the amount of time keys are stored before being deleted by default.
	 * As keys are usually stored for quite a while (in computer terms) but get
	 * old quickly (in human terms). So an average value of four hours is usually
	 * best.
	 */
	const DEFAULT_TIMEOUT = 14400; //4Hours
	
	/**
	 * The current instance of Memcached (instead of using several instances, to 
	 * avoid memory waste or conflicts when writing). This variable is only used
	 * on the static getInstance call.
	 * 
	 * @var \spitfire\MemcachedAdapter
	 */
	private static $instance = null;
	
	private $cache      = Array();
	private $timeout    = self::DEFAULT_TIMEOUT;
	private $connection = false;
	
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
	
	public function deleteKey($key) {
		if ($this->connection) {
			return $this->connection->delete($key);
		}
	}
	
	public function setTimeout($timeout) {
		$this->timeout = $timeout;
	}
	
	public function __get($var) {
		if (isset($this->cache[$var])) return $this->cache[$var];
		return $this->getKey($var);
	}
	
	/** 
	 * Simulates the use of memcached like a variable 
	 * i.e. <code>$mc->foo = "bar"</code>
	 * @param string $var Key to read
	 * @param mixed  $val Data to write to the key
	 */
	public function __set($var, $val) {
		return $this->cache[$var] = $val;
	}
	
	public function __unset($name) {
		unset($this->cache[$name]);
		$this->deleteKey($name);
	}
	
	public function __destruct() {
		foreach ($this->cache as $key => $value) 
			$this->setKey($key, $value);
	}
	
	public static function getInstance() {
		
		if (self::$instance === null) self::$instance = new \spitfire\MemcachedAdapter();
		return self::$instance;
	}
}