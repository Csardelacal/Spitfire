<?php

return [
	'url' => 'http://localhost/',
	'providers' => [
		spitfire\core\router\providers\RouterServiceProvider::class,
		\app\services\router\RouteProvider::class,
		spitfire\core\http\URLServiceProvider::class,
		\app\services\LoggingServiceProvider::class,
		\spitfire\io\session\SessionProvider::class,
		\spitfire\mvc\providers\DirectorProvider::class,
		spitfire\storage\database\support\services\DatabaseServiceProvider::class,
		spitfire\storage\database\support\services\MigrationServiceProvider::class,
		spitfire\model\providers\ModelServiceProvider::class
	]
];
