<?php


/**
 * This class will autoinclude functions the application needs to run and return
 * the result
 */
class _SF_Invoke
{
	/**
	 * This function will try to call a requested function and return it's
	 * result.
	 * 
	 * @param string $function_name
	 * @param mixed $arguments
	 * @return mixed Result of the called function
	 * @throws \spitfire\exceptions\PrivateException
	 */
	public function __call($function_name, $arguments) {
		if ( !is_callable($function_name) ) {
			$file = 'bin/functions/'.$function_name.'.php';
			if (file_exists($file)) include $file;
			else throw new \spitfire\exceptions\PrivateException('Undefined function: '. $function_name);
		}
		
		return call_user_func_array($function_name, $arguments);
		
	}
}