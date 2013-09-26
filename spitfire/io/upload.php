<?php

namespace spitfire\io;

use \filePermissionsException;

/**
 * This class merges the file Uploads coming from a client into the POST array,
 * allowing beans and programmers to have easier access to the data coming from
 * the client without trading in any security.
 * 
 * The class should not automatically store any data to avoid the user being able 
 * to inject uploads where unwanted. The class automatically names uploads when
 * storing to avoid collissions, returning the name of the file it stored.
 * 
 * @author César de la Cal <cesar@magic3w.com>
 * @since 0.1
 * @last-revision 2013.07.01
 */
class Upload
{
	private $meta;
	private $stored;
	private $uploadDir;
	
	public function __construct($meta) {
		$this->meta      = $meta;
		$this->uploadDir = 'bin/usr/uploads';
	}
	
	public function store() {
		if (is_array($this->meta['name'])) throw new \privateException('Is an upload array');
		
		if (empty($this->meta['name'])) throw new \privateException('Nothing uploaded');
		if ($this->meta['error'] > 0  ) throw new \privateException('Upload error');
		if ($this->stored) return $this->stored;
		
		if (!is_dir($this->uploadDir)) {
			if (!mkdir($this->uploadDir, 0777, true)) 
				throw new filePermissionsException('Upload directory does not exist and could not be created');
		}
		elseif (!is_writable($this->uploadDir)) {
			throw new filePermissionsException('Upload directory is not writable');
		}
		
		$filename = $this->uploadDir . '/' . base_convert(time(), 10, 32) . '_' . base_convert(rand(), 10, 32) . '_' . $this->meta['name'];
		
		move_uploaded_file($this->meta['tmp_name'], $filename);
		return $this->stored = $filename;
	}
	
	public function getData() {
		
		if (!is_array($this->meta['name'])) {
			if ($this->meta['size'] == 0) return null;
			return $this;
		}
		
		$_return = Array();
		$keys    = array_keys($this->meta['name']);
		
		foreach ($keys as $name) {
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
		
		$_POST = array_replace_recursive ($_POST, $files);
		
	}
	
}
