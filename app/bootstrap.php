<?php

/*
 * Load the environment file. Since bootstrap is ALWAYS located within the root
 * of the app folder, the environments file is always located within the folder 
 * above it.
 * 
 * The environment file should only contain settings that are "safe" to modify
 * by a user. For example, the DB connection string of a blog, or the connection
 * details to your SMTP server.
 * 
 * The environment should not allow setting configuration that generates unexpected
 * behavior in the application. These should be performed elsewhere.
 */
spitfire\core\Environment::import(parse_ini_file(dirname(__DIR__) . '/.environment'));

/*
 * The base directory defines the location in which the framework should look for
 * files that are not PHP classes. This includes the webroot folder, the assets,
 * resources, translations, templates, etc.
 */
define ('BASEDIR', rtrim(spitfire\core\Environment::get('application.basedir')?: dirname(__DIR__),'\/'));
