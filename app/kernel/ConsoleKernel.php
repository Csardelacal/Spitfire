<?php namespace app\kernel;

use spitfire\_init\LoadConfiguration;
use spitfire\_init\ProvidersInit;
use spitfire\_init\ProvidersRegister;
use spitfire\core\kernel\ConsoleKernel as CoreConsoleKernel;

class ConsoleKernel extends CoreConsoleKernel
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
		];
	}
}
