<?php

class Image
{
	private $img;
	private $meta;
	
	public function __construct($file) {
		$this->img = $this->readFile($file);
	}
	
	public function readFile($file) {
		
		$this->meta = $meta = getimagesize($file);
		
		switch($meta[2]) {
			case IMAGETYPE_PNG: 
				return imagecreatefrompng($file);
				break;
			case IMAGETYPE_JPEG: 
				return imagecreatefromjpeg($file);
				break;
			default:
				throw new privateException('Not supported image type');
		}
		
	}
	
	public function crop($x1, $y1, $x2, $y2) {
		
		$width  = $x2 - $x1;
		$height = $y2 - $y1;
		
		$img = imagecreate($width, $height);
		imagecopy($img, $this->img, 0, 0, $x1, $y1, $width, $height);
		
		$this->img = img;
		return $this;
		
	}
	
	public function fitInto ($width, $height) {
		
		$wider = ($this->meta[0] / $width) > ($this->meta[1] / $height);
		
		if ($wider) {
			$ratio    = $this->meta[1] / $height;
			$offset_x = ($this->meta[0] - $width * $ratio) / 2;
			$offset_y = 0;
		}
		else {
			$ratio    = $this->meta[0] / $width;
			$offset_y = ($this->meta[1] - $height * $ratio) / 2;
			$offset_x = 0;
		}
		
		$img = imagecreatetruecolor($width, $height);
		imagecopyresampled($img, $this->img, 0, 0, $offset_x, $offset_y, $width, $height, $this->meta[0]-2*$offset_x, $this->meta[1]-2*$offset_y);
		$this->img = $img;
		
		return $this;
		
		
	}
	
	public function store ($file) {
		if (file_exists($file)) unlink ($file);
		
		imagepng($this->img, $file, 0);
		return $file;
	}
}
