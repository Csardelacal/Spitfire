<?php

return [
	'providers' => [
		\app\services\router\RouteProvider::class,
		\app\services\LoggingServiceProvider::class,
		\spitfire\io\session\SessionProvider::class,
		\spitfire\mvc\providers\DirectorProvider::class
	]
];
