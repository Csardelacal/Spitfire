<?php

use spitfire\environment;

abstract class FileCache
{
	const DEFAULT_TIMEOUT = 14400;
	
	protected $cached;
	
	private $filename;
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