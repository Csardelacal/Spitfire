<?php namespace spitfire\io\image;

use spitfire\exceptions\FileNotFoundException;
use spitfire\exceptions\PrivateException;


class PNGQuant
{
	
	/**
	 * The PNGQuant function will automatically compress a PNG image, taking just the 
	 * path as a parameter. Since it will write the file to the exact same location
	 * that your original file was located, you don't need to do any additional work
	 * to write it.
	 * 
	 * Usually we don't compress originals, to maintain a "as good as possible" copy,
	 * but apply this to thumbs and versions we generated with the rather crummy GD
	 * compression, so you can optionally pass a second parameter to write to a 
	 * different file.
	 * 
	 * @param $img    string The file to read in
	 * @param $target string The file to write to
	 */
	public static function compress($img, $target = null) {
		if (!file_exists($img)) {
			throw new FileNotFoundException("File does not exist: $img");
		}
		
		$content = shell_exec("pngquant --quality=60-90 - < ".escapeshellarg(realpath($img)));

		if (!$content) {
			throw new PrivateException("Conversion to compressed PNG failed. Is pngquant 1.8+ installed on the server?");
		}

		file_put_contents($target? : $img, $content);
		return $img;
	}
}

