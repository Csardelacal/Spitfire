<?php

use spitfire\storage\database\drivers\mysqlpdo\Driver as MysqlPDO;

return [
	'mysql' => [
		'name'   => 'mysql',
		'driver' => MysqlPDO::class,
		'settings' => [
			'server' => 'mysql',
			'user' => 'www',
			'password' => 'test',
			'schema' => 'testdb'
		]
	]
];
