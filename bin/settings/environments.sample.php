<?php

use spitfire\core\Environment;

/*
 * Creates a test environment that can be used to store configuration that affects
 * the behavior of an application.
 */
$e = new Environment('test');
$e->set('db', 'mysqlpdo://root:root@localhost/database?encoding=utf8&prefix=');
$e->set('SSO', 'https://AppID:AppSecret@IP/phpas/');
