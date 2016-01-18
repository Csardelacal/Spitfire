<?php namespace spitfire\core;

/**
 * Environments are a way to store multiple settings for a single application
 * and several machines. We can even set automatic environment detection to 
 * make the process of switching the settings for several servers seamless.
 * 
 * @author  César de la Cal <cesar@magic3w.com>
 */

class Environment
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
	    
		#Request settings
		'supported_view_extensions'=> Array('php', 'xml', 'json'),
		'request.replace_globals'  => true,
		
		#Memcached settings
		'memcached_enabled'        => false,
		'memcached_servers'        => Array('localhost'),
		'memcached_port'           => '11211',
		 
		 #CacheFile settings
		 'cachefile.directory'     => 'bin/usr/cache/',
	    
		#Timezone settings
		'timezone'                 => 'Europe/Berlin',
		'datetime.format'          => 'd/m/Y H:i:s'
		
	);
	
	/**
	 * The array of declared environments. This allows the user to easily define
	 * several configurations that they can manage with ease.
	 *
	 * @var Environment[]
	 */
	static    $envs               = Array();
	
	/**
	 * The environment currently being used by the system. This is only used by the
	 * singleton methods and should be avoided in multi-head and multi-context
	 * environments.
	 *
	 * @var Environment|null
	 */
	static    $active_environment = null;
	
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
		$low = strtolower($key);
		$this->settings[$low] = $value;
	}
	
	/**
	 * Defines which environment should be used to read data from it.
	 * @param string|Environment $env The environment to be used.
	 */
	public static function set_active_environment ($env) {
		if (is_a($env, __class__) )                          { self::$active_environment = $env; }
		elseif (is_string($env) && isset(self::$envs[$env])) { self::$active_environment = self::$envs[$env]; }
	}
	
	/**
	 * Returns the selected key from the settings.
	 * @param string $key The key to be returned.
	 */
	public function read($key) {
		$low = strtolower($key);
		if (isset( $this->settings[$low] )) { return $this->settings[$low]; }
		else { return false; }
	}
	
	/**
	 * Static version of read. Will return the selected key from the currently 
	 * active environment.
	 * 
	 * @param string $key The key to be returned.
	 */
	public static function get($key = null) {
		#If no key was set we're expecting the system to return the active environment
		if ($key === null) { return self::$active_environment? : new self('default');}
		
		if (self::$active_environment) { return self::$active_environment->read($key); }
		#Implicit else
		new self('default');
		return self::get($key); //Repeat
	}
	
}
