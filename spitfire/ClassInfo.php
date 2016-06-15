<?php namespace spitfire;

/**
 * This class is actually deprecated. It is used by early versions of Spitfire to
 * locate classes. This is deprecated in favor of using several locators that can
 * find classes depending on how they end.
 * 
 * @deprecated since version 0.1-dev 20151106
 * @todo Finish implementing locators
 * @todo Move includeifpossible method somewhere it belongs.
 */
class ClassInfo
{
	const TYPE_CONTROLLER = 'Controller';
	const TYPE_COMPONENT  = 'Component';
	const TYPE_MODEL      = 'Model';
	const TYPE_RECORD     = 'Record';
	const TYPE_VIEW       = 'View';
	const TYPE_LOCALE     = 'Locale';
	const TYPE_APP        = 'App';
	const TYPE_BEAN       = 'Bean';
	const TYPE_STDCLASS   = '';
	
	
	/**
	 * Locates a file and includes it if possible. Otherwise it will just silently
	 * return false. 
	 * 
	 * This allows applications to either handle errors with includes without issue
	 * or to ignore the potential error situation that could arise.
	 * 
	 * @param string $file
	 * @return boolean|mixed
	 */
	public static function includeIfPossible($file) {
		if (file_exists($file)) { return include $file; }
		else { return false; }
	}
	
}
