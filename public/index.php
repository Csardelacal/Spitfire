<?php

/**
 * This is the main file of Spitfire, it is in charge of loading
 * system settings (for custom operation) and also of summoning
 * spitfire and loading the adequate controller for every single
 * request. It also makes sure that error logging is sent to
 * terminal / log file instead of to the user.
 * 
 * @author César de la Cal <cesar@magic3w.com>
 * @copyright 2021 Magic3W - All rights reserved
 */

use spitfire\core\Request;

/* 
 * Include Spitfire core.
 */
include __DIR__ . '/../vendor/autoload.php';
include __DIR__ . '/../app/constants.php';
include __DIR__ . '/../app/bootstrap.php';

/*
 * Spitfire will retrieve the request from the web server, select the appropriate
 * controller and invoke the middleware.
 */
$kernel = spitfire()->provider()->get(\spitfire\core\kernel\WebKernel::class);
emit(boot($kernel)->handle(Request::fromGlobals()));
