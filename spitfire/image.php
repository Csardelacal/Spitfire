<?php

use spitfire\exceptions\PrivateException;

/**
 * 
 * @deprecated since version 0.1-dev 1602242351
 */
class Image
{
	private $img;
	private $meta;
	private $compression = 0;
	
	public function __construct($file) {
		$this->img = $this->readFile($file);
	}
	
	public function readFile($file) {
		
		$this->meta = $meta = getimagesize($file);
		
		if (!function_exists('imagecreatefrompng')) {
			throw new PrivateException("GD is not installed.");
		}
		
		switch($meta[2]) {
			case IMAGETYPE_PNG: 
				$img = imagecreatefrompng($file);
				imagealphablending($img, false);
				imagesavealpha($img, true);
				return $img;
			case IMAGETYPE_JPEG: 
				return imagecreatefromjpeg($file);
			case IMAGETYPE_PSD:
				if (class_exists("Imagick")) {
					set_time_limit(480);
					$img = new Imagick();
					$img->readimage($file . '[0]');
					$img->setImageIndex(0);
					return $img;
				}
				throw new PrivateException('Spitfire requires Imagemagick to handle PSD files');
			default:
				throw new PrivateException('Not supported image type: ' . $meta[2]);
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
		
		if ($this->img instanceof Imagick) {
			$this->img->cropthumbnailimage($width, $height);
			return $this;
		}
		
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
		imagecolortransparent($img , imagecolorallocatealpha($img , 0, 0, 0, 127));
		imagealphablending($img, false);
		imagesavealpha($img, true);
		imagecopyresampled($img, $this->img, 0, 0, $offset_x, $offset_y, $width, $height, $this->meta[0]-2*$offset_x, $this->meta[1]-2*$offset_y);
		$this->img = $img;
		
		return $this;
		
		
	}
	
	public function setCompression($compression) {
		$this->compression = $compression;
	}
	
	public function getCompression() {
		return $this->compression;
	}
	
	public function store ($file) {
		if ($this->img instanceof Imagick) {
			if (file_exists($file)) unlink ($file);
			$this->img->setImageFormat('png');
			$this->img->writeimage(getcwd() . '/' . $file);
		} else {
			if (file_exists($file)) unlink ($file);
			imagepng($this->img, $file, $this->compression);
		}
		return $file;
	}
}
