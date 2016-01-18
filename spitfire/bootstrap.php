<?php

/*
 * This is the bootstrap file of spitfire. It imports all the basic files that 
 * are required for Spitfire to run.
 * 
 * It also creates the Autoload and ExceptionHandler instances that Spitfire will
 * use to retrieve classes that can be used by the user. This happens in the 
 * following order:
 * 
 * * Include core files
 * * Create autoload and Exception handler
 * 
 * This file does deliberately not import the user settings nor does it start
 * Spitfire. It will just prepare the components and files that Spitfire will need
 * in case it is invoked.
 * 
 * Usually, when working on a website index.php will instantly call spitfire()->light()
 * which will cause Spitfire to capture the Request from the webserver, handle it
 * and answer accordingly.
 */

#Start loading the core files.
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(dirname(__FILE__)));
require_once 'spitfire/exceptionHandler.php';
require_once 'spitfire/strings.php';
require_once 'spitfire/App.php';
require_once 'spitfire/Spitfire.php';
require_once 'spitfire/core/functions.php';
require_once 'spitfire/ClassInfo.php';  //TODO: Remove - Deprecated
require_once 'spitfire/autoload.php';
require_once 'spitfire/autoload/classlocator.php';
require_once 'spitfire/autoload/systemclasslocator.php';

#Create the exceptionhandler that will capture errors and try to present useful
#information to the user.
new spitfire\exceptions\ExceptionHandler();

#Create the autoload. Once started it will allow you to register classes and 
#locators to retrieve new classes that are missing to your class-space
new spitfire\AutoLoad();

#Import the locations of the most critical components to Spitfire so it has no
#need to look for them.
require_once 'spitfire/autoload_core_files.php';
