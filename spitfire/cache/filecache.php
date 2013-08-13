<?php

use spitfire\environment;

/**
 * The filecache class allows a user to create inheriting classes that contain
 * a onMiss method which's result will be cached to file in order to avoid
 * repeated calls to a function which may cause big delays due to high network
 * delays or due to high CPU / IO cost.
 * 
 * @author César de la cal <cesar@magic3w.com>
 * @last-revision 2013.07.11
 */
abstract class FileCache
{
	
	/**
	 * Defines the amount of time keys are stored before being deleted by default.
	 * As keys are usually stored for quite a while (in computer terms) but get
	 * old quickly (in human terms). So an average value of four hours is usually
	 * best.
	 */
	const DEFAULT_TIMEOUT = 14400;
	
	/**
	 * This is where the cached data is stored while executing to avoid having to 
	 * retrieve it from the file everytime getData() is called. On _destruct
	 * (and if the file did not exist) this data is written to a file.
	 *
	 * @var mixed
	 */
	protected $cached;
	
	/**
	 * The name of the file used to store the cached data. This file will later 
	 * hold a serialized version of an array called 'envelope'. The envelope will
	 * be composed of an expiry timestamp when the data is to be considered out
	 * of date and the data itself.
	 *
	 * @var string 
	 */
	private $filename;
	
	/**
	 * The directory where the cache is located. This variable helps avoiding the 
	 * object having problems locating the file if the environment changes during 
	 * runtime.
	 *
	 * @var string
	 */
	private $cache_dir;
	
	/**
	 * Full path to the cache file. This variable just simplifies writing
	 * code as it will no require to concat the directory and filename
	 * every time.
	 *
	 * @var string
	 */
	private $path;
	
	/**
	 * Contains the unix timestamp this cached resource expires. Please note
	 * that the cache will be taken as valid once before being destroyed in 
	 * order to reduce wait time for cached operations.
	 *
	 * @var int
	 */
	private $expires;
	
	/**
	 * Contains the amount of seconds this resource will be set to expire. In case
	 * the timeout is not altered it will default to 14400 (4 hours)
	 *
	 * @var int
	 */
	private $timeout = self::DEFAULT_TIMEOUT;
	
	/**
	 * Creates a new cache file. This allows you to store data to the disk that
	 * takes longer to generate / read from network / read from the DB. By doing
	 * so you reduce server load.
	 * 
	 * @param string $filename
	 * @throws privateException
	 */
	public function __construct($filename) {
		$this->filename  = $filename;
		$this->cache_dir = environment::get('cachefile.directory');
		$this->path      = spitfire()->getCWD() . '/' . rtrim($this->cache_dir, '/') . '/' . ltrim($this->filename, '/');
		
		if (!file_exists($this->cache_dir) && !mkdir($this->cache_dir, 0777, true) && !is_writable($this->cache_dir))
			throw  new privateException("Cache directory is not writable");
		
		if (file_exists($this->path))
			list($this->expires, $this->cached) = unserialize (file_get_contents ($this->path));
		else
			$this->cached  = $this->onMiss();
	}
	
	/**
	 * Replaces the currently cached data with anything the user defines for it.
	 * This is just meant for data that is cached and should change slightly over 
	 * time or be disposed.
	 * 
	 * @param mixed $data
	 */
	public function setCachedData($data) {
		$this->expires = null;
		$this->cached  = $data;
	}
	
	/**
	 * Returns the data contained by this cache file. This can be any kind of data 
	 * that can be serialized. If there was a problem serializing it will return 
	 * null.
	 * 
	 * @return mixed
	 */
	public function getCachedData() {
		return $this->cached;
	}
	
	/**
	 * Time in seconds the cache file should take to expire, after that amount of 
	 * seconds the next cache hit will delete it. The default value is 14400 which 
	 * corresponds to 4 hours.
	 * 
	 * @param int $timeout
	 */
	public function setTimeout($timeout) {
		$this->timeout = $timeout;
	}
	
	/**
	 * Writes the cached data to disk. This generates an envelope with the expiry
	 * timestamp and the mixed data the file holds and serializes it to disk.
	 * 
	 * @throws privateException If the lock for the file could not be acquired.
	 */
	public function writeToDisk() {
		$envelope = Array($this->expires, $this->cached);
		$fh = fopen($this->path, 'w+');
		
		if ($fh) {
			if (flock($fh, LOCK_EX)) {
				fwrite($fh, serialize($envelope));
				flock($fh, LOCK_UN);
			}
			else {
				throw new privateException("Lock could not be acquired for " . $this->path);
			}
		}
	}

	/**
	 * This method needs to be implemented by child classes in order to tell the
	 * cache how to generate the data it requires. The return value of this method 
	 * will be stored into the file.
	 * 
	 * Please note that the returned value of this class needs to be serializable.
	 * Otherwise it will fail.
	 */
	public abstract function onMiss();
	
	/**
	 * This method is in charge of handling the destruction of the class. This 
	 * usually allows to defer the writing on the file to the end of the execution
	 * of the script, therefore not creating any delay on te users end due to 
	 * disk IO.
	 */
	public function __destruct() {
		if ($this->expires == null) {
			$this->expires = time() + $this->timeout;
			$this->writeToDisk();
		}
		
		if (time() > $this->expires) {
			unlink($this->path);
		}
	}
	
	
}