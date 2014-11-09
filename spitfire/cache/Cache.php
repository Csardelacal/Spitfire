<?php namespace spitfire\cache;

/**
 * The cache interface should provide a consistent way to provide caching with 
 * different mechanisms.
 */
interface Cache
{
	
	/**
	 * The get function should allow a user to quickly read and write data from
	 * a cache. 
	 * 
	 * You can provide the optional callback parameter in order to generate the 
	 * value if the cache misses. This reduces code complexity on the user side, 
	 * instead of the user writing an assertion like this:
	 * 
	 * <code>if (false !== $v = $cache->get('key')) { //Do something }</code>
	 * 
	 * You can write code like 
	 * <code>$v = $cache->get('key', function (){ //Get the value here});</code>
	 * 
	 * [NOTICE] In future revisions of Spitfire, this code should also accept 
	 * database aggregate functions in order to speed the development of applications
	 * that need complex queries to be cached.
	 * 
	 * @param string $key
	 * @param null|int|string|\Closure $fallback
	 */
	public function get($key, $fallback = null);
	public function set($key, $value);
	public function delete ($key);
	
}