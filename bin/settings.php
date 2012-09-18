<?php


/* MAINTENANCE
 * This settings define whether the engine is down
 * for maintenance and what controller should be
 * used for this.
 * When in maintenance mode index will not send requests
 * to any controller that is not the one defined.
 */
define('maintenance_enabled', false, true);
define('maintenance_controller', 'maintenance', true);

/* DATABASE SETTINGS
 * This sets many of the default database settings
 * #I should come up with better descriptions#
 */
define('table_prefix', 'xobs_', true);
define('db_server', 'localhost', true);
define('db_user', 'root', true);
define('db_password', 'perrakiara', true);
define('db_database', 'xobs', true);

/* MVC SETTINGS
 * This settings alter the behaviour of the mvc engine
 * and how it works.
 * Here you can set default controllers / views / actions
 * and whether to use or not pretty urls (without filenames)
 */
define('pretty_urls', true, true);
define('default_controller', 'home', true);
define('default_action', 'index', true);
define('default_object', '', true);

/*
 * MEMCACHED SETTINGS
 * Determines whether memcached is enabled / disabled and
 * what servers the script has available for use. By default
 * memcached should be enabled to allow best speed and 
 * effective caching on the servers.
 * Memcached should not be used deliberately and should at
 * best only be used when we need calculated values to be stored
 * that do not require search or similar.
 */
define ('memcached_enabled', true, true);
$memcached_servers = Array('localhost');

/*
 * URL SETTINGS
 * By default we will try to get the base_url of our script 
 * automatically. As this is not a perfect way of detecting
 * base urls (neither it is the fastest one) we'll leave it
 * to the administrator to manually setup a value or use the
 * provided tool.
 */
list($base_url) = explode('/index.php', $_SERVER['PHP_SELF'], 2);
define('base_url', $base_url, true);


/*
 * SESSIONS
 */
define ('COOKIE_DOMAIN', 'emachines.lan');