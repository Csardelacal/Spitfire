<?php

namespace spitfire;

/**
 * Environments are a way to store multiple settings for a single application
 * and several machines. We can even set automatic environment detection to 
 * make the process of switching the settings for several servers seamless.
 * 
 * @package Spitfire.settings
 * @author  César de la Cal <cesar@magic3w.com>
 */

class environment
{
	/**
	 * Default settings. This array contains the settings that are predefined
	 * in Spitfire. They can later be overriden if needed by using set inside 
	 * of each environment.
	 */
	protected $settings = Array (
		#Maintenance related settings.
		'maintenance_enabled'      => false,
		'maintenance_controller'   => 'maintenance',
		'debugging_mode'           => true, //TODO: Change for stable
		
		#Database settings
		'db_driver'                => 'mysqlPDO',
		'db_server'                => 'localhost',
		'db_user'                  => 'root',
		'db_pass'                  => '',
		'db_database'              => 'database',
		'db_table_prefix'          => '',
	
		#Character encoding settings
		'system_encoding'          => 'utf-8',
		'database_encoding'        => 'latin1',
	    
		#MVC Related settings
		'pretty_urls'              => true,
		'default_controller'       => 'home',
		'default_action'           => 'index',
		'default_object'           => Array(),
	    
		#Content support
		'supported_view_extensions'=> Array('php', 'xml', 'json'),
		
		#Memcached settings
		'memcached_enabled'        => false,
		'memcached_servers'        => Array('localhost')
		
	);
	
	static    $envs               = false;
	static    $active_environment = false;
	
	/**
	 * When creating a new environment it'll be created with a name that will
	 * identify it and a set of default settings that can be overriden afterwards.
	 * 
	 * @param string $env_name Name of the environment i.e. Testing.
	 */
	public function __construct($env_name) {
		self::$envs[$env_name] = $this;
		self::$active_environment = $this;
	}
	
	/**
	 * This function creates / overrides a setting with a value defined by the
	 * developer. Names are case insensitive.
	 * @param string $key The name of the setting
	 * @param string $value The value of the parameter.
	 */
	public function set ($key, $value) {
		$key = strtolower($key);
		$this->settings[$key] = $value;
	}
	
	/**
	 * Defines which environment should be used to read data from it.
	 * @param string|environment $env The environment to be used.
	 */
	public static function set_active_environment ($env) {
		if (is_a($env, __class__) )        self::$active_environment = $env;
		else if (isset (self::$envs[$env])) self::$active_environment = self::$envs[$env];
	}
	
	/**
	 * Returns the selected key from the settings.
	 * @param string $key The key to be returned.
	 */
	public function read($key) {
		$key = strtolower($key);
		if (isset( $this->settings[$key] )) return $this->settings[$key];
		else return false;
	}
	
	/**
	 * Static version of read. Will return the selected key from the currently 
	 * active environment.
	 * @param string $key The key to be returned.
	 */
	public static function get($key) {
		if (self::$active_environment) return self::$active_environment->read($key);
		#Implicit else
		new environment('default');
		return self::get($key); //Repeat
	}
	
}
