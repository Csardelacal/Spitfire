<?php

/**
 * Storage related components of nLive
 * 
 * @package Spitfire.storage
 * @author César de la Cal <cesar@magic3w.com>
 */


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