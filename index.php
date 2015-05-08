<?php

/**
 * This is the main file of Spitfire, it is in charge of loading
 * system settings (for custom operation) and also of summoning
 * spitfire and loading the adequate controller for every single
 * request. It also makes sure that error logging is sent to
 * terminal / log file instead of to the user.
 * 
 * @package Spitfire
 * @author César de la Cal <cesar@magic3w.com>
 * @copyright 2012 Magic3W - All rights reserved
 */

/* Define bootstrap settings. Environments are a better way to handle
 * config but we need to create them first.
 */
define ('APP_DIRECTORY',         'bin/apps/',        true);
define ('CONFIG_DIRECTORY',      'bin/settings/',    true);
define ('CONTROLLERS_DIRECTORY', 'bin/controllers/', true);
define ('ASSET_DIRECTORY',       'assets/',          true);
define ('TEMPLATES_DIRECTORY',   'bin/templates/',   true);
define ('SESSION_SAVE_PATH',     'bin/usr/sessions/',true);

/* Set error handling directives. AS we do not want Apache / PHP
 * to send the data to the user but to our terminal we will tell
 * it to output the errors. Thanks to this linux command:
 * # tail -f *logfile*
 * We can watch errors happening live. Grepping them can also help
 * filtering.
 */
ini_set("log_errors" , "1");
ini_set("error_log" , "logs/error_log.log");
ini_set("display_errors" , "0");

/* Include Spitfire core.
 */
include 'spitfire/bootstrap.php';

/* SESSION DEFAULTS AND START_______________________________________
 * This sets basic settings about user sessions and their duration,
 * it enables the user to revisit the system after 24 hours without
 * logging in again.
 * 
 * grab a cookie from the user's machine and detect if the user has
 * a valid one. If he has he can enter the system. This cookie is made
 * of the user's Id and a random number we'll store into our DB. After
 * the time of 6 months the cookie should be removed.
 */
$month = 3600*24*30;
ini_set('session.gc_maxlifetime',$month);
ini_set('session.save_path', SESSION_SAVE_PATH);
ini_set('memory_limit', '128M');/**/

/* Call the selected controller with the selected method. */
spitfire()->fire();