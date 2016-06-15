<?php namespace spitfire\cache;

use spitfire\exceptions\PrivateException;

/**
 * This class allows you to set an arbitrary callback to use for a cache miss 
 * instead of forcing you to create a class that inherits from FileCache and 
 * implement just one or two methods.
 * 
 * @author César de la Cal <cesar@magic3w.com>
 * @last-revision 2015.11.09
 * 
 */
class SimpleFileCache extends FileCache
{
	/**
	 * Contains the callable item to retrieve the data for this cache. When the 
	 * cache 'misses' (tries to read the cache and does not find anything) it will
	 * call this.
	 *
	 * @var callable 
	 */
	private $callback;
	
	/**
	 * Instances a new class. Items of this class allow data to be retrieved
	 * from a file when retrieving data from a source that is slow or expensive
	 * for the CPU.
	 * 
	 * @param string $filename
	 * @param callable $callback
	 */
	public function __construct($filename, $callback) {
		$this->callback = $callback;
		parent::__construct($filename);
	}
	
	/**
	 * Calls the function the user has passed to the constructor in order to fetch
	 * data when needed.
	 * 
	 * @return mixed
	 * @throws PrivateException
	 */
	public function onMiss() {
		$callback = $this->callback;
		
		if (is_callable($callback)) {
			return $callback();
		} else {
			throw new PrivateException("No valid callback supplied");
		}
	}	
}
