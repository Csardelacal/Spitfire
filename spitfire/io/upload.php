<?php

namespace spitfire\io;

class Upload
{
	private $meta;
	private $stored;
	
	public function __construct($meta) {
		$this->meta = $meta;
	}
	
	public function store() {
		if (is_array($this->meta['name'])) throw new \privateException('Is an upload array');
		
		if (empty($this->meta['name'])) throw new \privateException('Nothing uploaded');
		if ($this->meta['error'] > 0  ) throw new \privateException('Upload error');
		if ($this->stored) return $this->stored;
		
		if (!is_dir('bin/usr/uploads/')) {
			if (!mkdir('bin/usr/uploads/')) throw new privateException('Upload directory does not exist and could not be created');
		}
		elseif (!is_writable('bin/usr/uploads/')) {
			throw new privateException('Upload directory is not writable');
		}
		
		$filename = 'bin/usr/uploads/' . base_convert(time(), 10, 32) . '_' . base_convert(rand(), 10, 32) . '_' . $this->meta['name'];
		
		move_uploaded_file($this->meta['tmp_name'], $filename);
		return $this->stored = $filename;
	}
	
	public function getData() {
		
		if (!is_array($this->meta['name'])) return $this;
		
		$_return = Array();
		foreach ($this->meta['name'] as $name => $ignore) {
			$_return[$name] = $this->$name->getData();
		}
		
		return $_return;
	}
	
	public function __get($name) {
		if (isset($this->meta['name'][$name])) {
			return new Upload(Array(
				 'name'     => $this->meta['name'][$name],
				 'type'     => $this->meta['type'][$name],
				 'tmp_name' => $this->meta['tmp_name'][$name],
				 'size'     => $this->meta['size'][$name],
				 'error'    => $this->meta['error'][$name],
			));
		}
	}
	
	public static function init() {
		
		$files   = $_FILES;
		
		foreach ($files as &$file) {
			$file = new Upload($file);
			$file = $file->getData();
			unset($file);
		}
		
		$_POST = array_merge_recursive ($_POST, $files);
		
	}
	
}
