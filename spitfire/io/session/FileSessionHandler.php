<?php namespace spitfire\io\session;

class FileSessionHandler extends SessionHandler
{
	
	private $directory;
	
	public function __construct($directory, $timeout = null) {
		$this->directory = $directory;
		parent::__construct($timeout);
	}
	
	public function close() {
		return true;
	}
	
	public function destroy($id) {
		$file = sprintf('%s/sess_%s', $this->directory, $id);
		file_exists($file) && unlink($file);
		
		return true;
	}
	
	public function gc($maxlifetime) {
		if ($this->getTimeout()) { $maxlifetime = $this->getTimeout(); }
		
		foreach (glob("$this->directory/sess_*") as $file) {
			if (filemtime($file) + $maxlifetime < time() && file_exists($file)) {
				unlink($file);
			}
		}

		return true;
	}
	
	public function open($savePath, $sessionName) {
		if (empty($this->directory)) { $this->directory = $savePath; }
		
		if (!is_dir($this->directory) && !mkdir($this->directory, 0777, true)) {
			throw new \spitfire\exceptions\FileNotFoundException($this->directory . 'does not exist and could not be created');
		}
		
		return true;
	}
	
	public function read($id) {
		$file = sprintf('%s/sess_%s', $this->directory, $id);
		
		if (!file_exists($file)) { return ''; }
		return (string)file_get_contents($file);
	}
	
	public function write($id, $data) {
		return file_put_contents(sprintf('%s/sess_%s', rtrim($this->directory, '\/'), $id), $data) !== false;
	}

}
