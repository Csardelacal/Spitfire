<?php

class SimpleFileCache extends FileCache
{
	
	private $callback;
	
	public function __construct($filename, $callback) {
		$this->callback = $callback;
		parent::__construct($filename);
	}

	public function onMiss() {
		$callback = $this->callback;
		
		if (is_callable($callback))
			return $callback();
		else
			throw new privateException("No valid callback supplied");
	}	
}
