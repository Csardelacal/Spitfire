<?php namespace spitfire\cache;

use spitfire\core\Environment;
use spitfire\exceptions\PrivateException;
use spitfire\storage\database\Query; //TODO: Introduce query interface that other vendors can implement
use Memcached;
use Closure;

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
class MemcachedAdapter implements CacheInterface
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
	 * @var MemcachedAdapter
	 */
	private static $instance = null;
	
	/**
	 * The environment this adapter should be retrieving it's settings from. This
	 * contains the settings the adapter will need to properly function.
	 *
	 * @var Environment
	 */
	private $environment = null;
	
	/**
	 * Contains the data cached. Array caches are way faster than even memcached,
	 * so putting the data retrieved / set into an array and saving it on destruction
	 * offers better performance.
	 *
	 * @var mixed
	 */
	private $cache      = Array();
	
	/**
	 * Number of seconds the keys take to time out. Spitfire uses on unified cache
	 * time to store all keys by default. This allows it to make storing keys 
	 * really comfortable.
	 *
	 * @var int
	 */
	private $timeout    = self::DEFAULT_TIMEOUT;
	
	/**
	 * The connection used to store the data on the Memcached server. Spitfire uses 
	 * this to send the data once the destruct hook is called and to retrieve
	 * data from the Memcached server.
	 *
	 * @var \Memcached
	 */
	private $connection = false;
	
	/**
	 * Creates a connection to the memcached servers.
	 * 
	 * @throws PrivateException
	 * @return \Memcached 
	 */
	public function connect () {
		#First we retrieve the list of Memcached servers
		$memcached_servers = $this->environment->read('memcached_servers');
		
		#If memcached is enabled we check if it is available
		if (!$this->environment->read('memcached_enabled')) { return; }
		if (!class_exists('\Memcached') ) { throw new PrivateException('Memcached is enabled but not installed'); }
		
		#Instance a new memcached connection
		$this->connection = new Memcached();
		
		#Add the array of servers we want to use
		foreach ($memcached_servers as $server) {
			$this->connection->addServer($server, $this->environment->read('memcached_port'));
		}
		
		#Return the connection.
		return $this->connection;
		
	}
	
	/**
	 * A MC instance will just check if MC Connection exists by calling 
	 * @uses MemcachedAdapter::connect();
	 */
	public function __construct($env = null){
		$this->environment = $env? : Environment::get();
		$this->connect();
	}
	
	/**
	 * Sends data to the memcached server. This function does NOT cache the request
	 * so if you use this instead of the default _get and _set you may see reduced
	 * performance but better synchronization on high R/W environments.
	 * 
	 * @param string $key
	 * @param mixed $value
	 * @return mixed
	 */
	public function set($key, $value) {
		if ($this->connection) {
			return $this->connection->set($key, $value, $this->timeout);
		}
	}
	
	/**
	 * Reads a key from the memcached server. This function does not cache it's
	 * result, if you require caching use object access.
	 * 
	 * @param string        $key
	 * @param \Closure|null $fallback
	 * @return boolean
	 */
	public function get($key, $fallback = null) {
		if ($this->connection) { 
			$cached = $this->connection->get($key);
			if ($this->connection->getResultCode() !== \Memcached::RES_NOTFOUND) { return $cached; }
			
			#No cached version available, cache the proposed data
			if ($fallback === null) { return false; }
			elseif ($fallback instanceof Closure) { $newval = $fallback(); }
			elseif ($fallback instanceof Query)   { $newval = $fallback->fetchAll(); }
			//TODO: Add a option for query aggregation functions
			else { $newval = null; }
			
			$this->set($key, $newval);
			return $newval;
		}
		
		#Implcit else since there is no cache
		return $fallback? $fallback() : false;
	}
	
	/**
	 * Deletes a key from the memcached server. Returns true on success and false
	 * on failure.
	 * 
	 * @param string $key
	 * @return boolean
	 */
	public function delete($key) {
		if ($this->connection) {
			return $this->connection->delete($key);
		}
	}
	
	/**
	 * Increments the value of a key on the memcached server. This function is
	 * very useful when using it within high read/write keys (like pageviews) that
	 * require instant updating on the server in order to make the values correct.
	 * 
	 * @param string $key
	 * @param int $amt
	 * @return boolean True on success
	 */
	public function increment($key, $amt = 1) {
		if ($this->connection) {
			return $this->connection->increment($key, $amt);
		}
	}
	
	/**
	 * Defines the timeout (in seconds) for the Memcached keys this class writes.
	 * Remember that cached keys will be affected by this on shutdown. This means
	 * that if you have a cached key you want to store with a different timeout
	 * you will need to manually write to MC.
	 * 
	 * @param int $timeout
	 */
	public function setTimeout($timeout) {
		$this->timeout = $timeout;
	}
	
	/**
	 * Returns the data stored in cache by the Server. This function uses the 
	 * cache to return data, be careful when mixing calls with this and get.
	 * 
	 * Be wary of the following:
	 * <code>
	 *		$mc->key = 5;
	 *		$mc->set('key', 2);
	 *		echo $mc->key;        //Output: 5 
	 *		echo $mc->get('key'); //Output: 2 
	 * </code>
	 * Additionally, the cache will store 5 to the server. Even if it was set 
	 * earlier. This is due to the cache being written on end of the script.
	 * 
	 * @param string $var
	 * @return mixed
	 */
	public function __get($var) {
		if (isset($this->cache[$var])) { return $this->cache[$var]; }
		return $this->get($var);
	}
	
	/** 
	 * Simulates the use of memcached like a variable  
	 * <code>
	 *		$mc->foo = "bar"
	 * </code>
	 * @param string $var Key to read
	 * @param mixed  $val Data to write to the key
	 */
	public function __set($var, $val) {
		return $this->cache[$var] = $val;
	}
	
	/**
	 * Deletes a record from the memcached when unsetting it from the object.
	 * This allows to delete keys by using unset($mc->key)
	 * 
	 * @param string $name
	 */
	public function __unset($name) {
		unset($this->cache[$name]);
		$this->delete($name);
	}
	
	/**
	 * Stores the data when the object is destroyed. This allows Spitfire to 
	 * improve speed of low R/W keys by caching the results in loca memory
	 * instead of directly writing the data.
	 * 
	 * @uses \spitfire\MemcachedAdapter::set()
	 */
	public function __destruct() {
		foreach ($this->cache as $key => $value) {
			$this->set($key, $value);
		}
	}
	
	/**
	 * Singleton factory for MemcachedAdapter. This avoids memory waste by creating 
	 * several Memcached instances and cache collissions when writing to the 
	 * tool.
	 * 
	 * @return MemcachedAdapter
	 */
	public static function getInstance() {
		if (self::$instance === null) { self::$instance = new MemcachedAdapter(); }
		return self::$instance;
	}

	public function contains($key) {
		//TODO: Implement
		return true;
	}

}
