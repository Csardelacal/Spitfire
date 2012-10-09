<?php

/**
 * Storage related components of nLive
 * 
 * @package nLive.storage
 * @author César de la Cal <cesar@magic3w.com>
 */

/**
 * Memcached interface
 * @package nLive.storage
 */
class _SF_Memcached
{
	const DEFAULT_TIMEOUT = 14400; //4Hours
	
	protected $timeout    = 0;
	protected $connection = false;
	
	/**
	 * Creates a connection to the memcached servers.
	 * @uses $memcached_servers List of servers used by this function
	 * @return memcache 
	 */
	public function connect () {
		$memcached_servers = environment::get('memcached_servers');
		
		
		if (! MEMCACHED_ENABLED) return;
		if (!class_exists('memcache') ) throw new privateException('Memcached is enabled but not installed');
		
		$this->connection = new memcache();
		foreach ($memcached_servers as $server) $this->connection->addServer($server);
		return $this->connection;
		
	}
	
	/**
	 * A MC instance will just check if MC Connection exists by calling 
	 * @uses memcached::getConnection();
	 */
	public function __construct($timeout = false){
		
		if (!$timeout) $this->timeout = self::DEFAULT_TIMEOUT;
		else $this->timeout = $timeout;
		
		$this->connect();
	}
	
	public function setKey($key, $value) {
		if ($this->connection) {
			return $this->connection->set($key, $value, false, $this->timeout);
		}
	}
	
	public function getKey($key) {
		if ($this->connection) {
			return $this->connection->get($key);
		}
	}
	
	public function newTimeout($timeout) {
		return new _SF_Memcached($timeout);
	}
	
	/** 
	 */
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
		return $this->setKey($var, $val);
	}
}

/**
 * @package nLive.storage
 */

class DBO
{
	const VALIDATION_PREFIX = 'validate_';
	
	function __get($key) {
		$t = $this->data[$key];
		
		//Standard checks
		$t = htmlentities(strip_tags($t));
		$t = nl2br($t);
		
		//Check if we need to parse the data in any other way.
		$method = self::PARSING_PREFIX . strtolower($key);
		$callable = is_callable( Array($this, $method) );
		if ( $callable ) $t = call_user_func_array(Array($this, $method), Array($t) );
		
		return $t;
	}
	
}



/**
 * Singleton for PDO database. Instead of reconnecting everytime we need it we just call
 * PDO().
 * @return PDO Database Object;
 * @package nLive.storage
 */

function PDO() { //TODO: We could also add a "DBid" which would allow to use the application to communicate with several DBs
	static $pdo;
	if (!$pdo) $pdo = new PDO( 'mysql:host='.DB_SERVER.';dbname='. DB_DATABASE, DB_USER, DB_PASSWORD);
	return $pdo;
}





/**
 * Data URI encoding is used to improve CSS performance when loading small images
 * by embedding them directly into the CSS file itself, reducing the ammount of
 * HTTP requests
 * @param String $asset File location of the asset
 * @param String $mime  Content/type
 * @return String URI encoded data
 * @package nLive.storage
 */

function dataURI($asset, $mime = 'image/png', $charset = false) {
	$data = file_get_contents($asset);
	$base64 = base64_encode($data);
	$charset = ($charset)? "charset=$charset" : '';
	return "data:$mime;$charset;base64,$base64";
}




class thumb
{
	private $image;
	
	private $src_x = 0;
	private $src_y = 0;
	private $src_w = 0;
	private $src_h = 0;
	
	public function __construct(&$image)
	{
		$this->image = $image;
	}
	
	protected function calculate_dimensions()
	{
		if ( imagesx($this->image) > imagesy($this->image) )
		{
			$this->src_x = ( imagesx($this->image) - imagesy($this->image) ) / 2;
			$this->src_y = 0;
			$this->src_w = $this->src_h = imagesy($this->image); 
		}
		else
		{
			$this->src_y = ( imagesy($this->image) - imagesx($this->image) ) / 2;
			$this->src_x = 0;
			$this->src_w = $this->src_h = imagesx($this->image); 
		}
	}
	
	public function makeThumb ($size, $file)
	{
		$this->calculate_dimensions();
		
		$thumb = imagecreatetruecolor($size, $size);
		$white = imagecolorallocate($thumb, 255, 255, 255);
		imagefill($thumb, 0, 0, $white);
		
		$success = imagecopyresampled($thumb, $this->image, 0, 0, $this->src_x, $this->src_y, $size, $size, $this->src_w, $this->src_h);
		
		if ($success) imagepng($thumb, $file);
		
		return $success;
	}
	
}







class imageUpload
{
	
	private $mimes = Array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/png');
	private $file;
	
	public function __construct($file)
	{
		$this->file = $file;
	}
	
	private function isValid()
	{
		if (isset ($this->file['type']) )
		{
			if ( !($this->file['error'] > 0) )
			{
				if ( $this->file['size'] < (1*1024*1024) ) // 1MB
				return !!( in_array($this->file['type'], $this->mimes) );
			}
		}
		else return false;
	}
	
	private function makeImage()
	{
		$file = $this->file;
		switch ($file['type'])
		{
			case 'image/gif'  : $im = imagecreatefromgif($file["tmp_name"]); break;
			case 'image/jpeg' : $im = imagecreatefromjpeg($file["tmp_name"]); break;
			case 'image/pjpeg': $im = imagecreatefromjpeg($file["tmp_name"]); break;
			case 'image/png'  : $im = imagecreatefrompng($file["tmp_name"]); break;
			
			default: return false;
		}
		return $im;
	}
	
	public function getImage()
	{
		if ( $this->isValid() )
		{
			return $this->makeImage();
		}
	}
	
	public function store ($destination)
	{
		if ($this->isValid())
		{
			if (file_exists($destination)) unlink($destination);
			
			return move_uploaded_file($this->file['tmp_name'], $destination);
		}
	}
}