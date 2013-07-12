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
	private $path;
	private $expires;
	private $timeout = self::DEFAULT_TIMEOUT;
	
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
	
	public function setCachedData($data) {
		$this->cached = $data;
	}
	
	public function getCachedData() {
		return $this->cached;
	}
	
	public function setTimeout($timeout) {
		$this->timeout = $timeout;
	}
	
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

	public abstract function onMiss();
	
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