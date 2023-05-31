<?php namespace app\kernel;

use spitfire\_init\InitRequest;
use spitfire\_init\LoadConfiguration;
use spitfire\_init\ProvidersInit;
use spitfire\_init\ProvidersRegister;
use spitfire\core\kernel\WebKernel as CoreWebKernel;

class WebKernel extends CoreWebKernel
{
	
	
	/**
	 * The list of init scripts that need to be executed in order for the kernel to
	 * be usable.
	 *
	 * @return string[]
	 */
	public function initScripts(): array
	{
		return [
			LoadConfiguration::class,
			ProvidersRegister::class,
			ProvidersInit::class,
			InitRequest::class
		];
	}
}
