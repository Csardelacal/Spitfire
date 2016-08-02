<?php namespace spitfire\io;

use finfo;
use spitfire\exceptions\FileNotFoundException;

class DataURI
{
	
	private $content;
	private $mimetype;
	private $charset;
	
	public function __construct($content, $mimetype, $charset = null) {
		$this->content = $content;
		$this->mimetype = $mimetype;
		$this->charset = $charset? $charset : mb_internal_encoding();
	}
	
	public function __toString() {
		return sprintf('data:%s;%s;base64,%s', 
			$this->mimetype, $this->charset, base64_encode($this->content));
	}
	
	/**
	 * Data URI encoding is used to improve CSS performance when loading small images
	 * by embedding them directly into the CSS file itself, reducing the ammount of
	 * HTTP requests
	 * 
	 * These saved requests are useful when the overhead of roughly 30% additional
	 * payload is negligible compared with the cost of a roundtrip. And they're
	 * also useful when the server is having issues responding (for example, the
	 * webserver is returning 404 / 500 pages for all requests - the served page
	 * should not be using any external resources from the same server).
	 * 
	 * @param string $file
	 * @return DataURI
	 * @throws FileNotFoundException
	 */
	public static function fromFile($file) {
		#Check if the file exists in the first place
		if (!file_exists($file)) { throw new FileNotFoundException($file . ' does not exist'); }
		
		#Retrieve the mime type
		$finfo = new finfo(FILEINFO_MIME_TYPE);
		$mimetype = $finfo->file($file);
		
		return new DataURI(file_get_contents($file), $mimetype);
	}
	
}

