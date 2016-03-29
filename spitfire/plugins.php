<?php

/**
 * This files contains all the classes that help
 * improving nLive or the software based on it 
 * without altering the framework itself.
 * 
 * @author César de la Cal <cesar@magic3w.com>
 * @package Spitfire
 *
 */


class plugins
{
	
	//Preset default plugins
	const PRESET_URLTOSTRING = 'url_to_string';
	
	/**
	 * This array stores the list of plugins currently loaded.
	 */
	private $plugins = Array(); 
	
	/**
	 * Registers a new plugin, this is automatically called
	 * from the plugin constructor if it's not overriden. If
	 * you use it on your plugin please remmeber to call parent::
	 * __contruct.
	 * 
	 * @param plugin $plugin
	 */
	public function register(plugin $plugin) {
		$this->plugins[] = $plugin;
	}
	
	public function __call($hook, $args) {
		if (is_array($args) && count($args) == 1) $args = $args[0];
		foreach($this->plugins as $plugin) {
			$method = Array($plugin, $hook);
			if (is_callable($method)) 
				if ($t = $plugin->$hook($args))
					$args = $t;
		}
		return $args;
	}
	
}

abstract class plugin
{
	
	public function __construct() {
		plugins::register($this);
	}
	
	public function __call($function, $args) {
		return false;
	}
	
	abstract public function pluginInfo();
	abstract public function pluginVersion();
	
}

/**
 * This class is basically an Array cache, this means that
 * you can define stuff to use it later.
 * 
 * This is especially useful when using if for static elements
 * like CSS, breadcrumbs and scripts.
 * 
 * @author C�sar
 *
 */
class registry
{
	private static $data = Array();
	
	const TYPE_CSS = 'css';
	const TYPE_SCRIPT = 'script';
	const TYPE_SCRIPT_LIB  = 'script_lib';
	const TYPE_BREADCRUMBS = 'breadcrumbs';
	
	public static function store ($key, $value, $append = false) {
		if ($append) self::$data[$key][] = $value;
		else self::$data[$key] = $value;
	}
	
	public static function read ($key) {
		if (isset (self::$data[$key]) ) return self::$data[$key];
		else return false;
	}
	
	public static function get_as($key, $type) {
		switch ($type) {
			case self::TYPE_CSS:
				$t = self::read($key);
				$str = '';
				if ($t !== false && is_array($t)) {
					foreach ( $t as $v) $str.='<link rel="stylesheet" type="text/css" href="'.$v.'" />';
				}
				return $str;
				break;
				
			case self::TYPE_SCRIPT:
			case self::TYPE_SCRIPT_LIB:
				$t = self::read($key);
				$str = '';
				if ($t !== false && is_array($t)) {
					foreach ( $t as $v) $str.='<script type="text/javascript" src="'.$v.'" ><script>';
				}
				break;
				
			default:
				return self::read($key);
		}
	}	
}

/**
 * This allows us to parse and obtain data from .m3w containers.
 * 
 * .m3w containers are really simple cofiguration files that can return
 * arrays without being as complicated and strict as .xml or .php files.
 * Basically they're really easy, each line of them represents a piece of
 * information. If the line starts with an @ it adds the data to a key, if
 * preceeded by a # it will detect it as comment. The data looks like a phpdoc
 * piece of information.
 * 
 * @author C�sar <cesar@magic3w.com>
 *
 */
class m3w_parser
{
	private $content = Array();
	private $textContent = '';
	
	protected function addField($data) {
		list($key, $value) = explode(' ', $data, 2);
		$this->content[$key][] = $value;
	}
	
	protected function parse($data) {
		foreach ($data as $config) {
			$first_char = substr($config, 0, 1);
			switch ($first_char) {
				case '@':
					$this->addField(substr($config, 1));
					break;
				case '#':
					//Do nothing, it's a /*trap*/ comment
					break;
				default:
					$textContent.= "$config\n";
					break;
			}
		}
	}
	
	public function __construct($file) {
		if (file_exists($file)) $c = file($file);
		else throw new \spitfire\exceptions\FileNotFoundException('File '.$file.' not found', 0);
		$this->parse($c);
	}
	
	public function getKey($key) {
		if (isset($this->content[$key])) return $this->content[$key];
		else return false;
	}
	
	public function getSingle($key) {
		if ($t = $this->getKey($key) ) return $t[0];
		else return false;
	}
	
	public function getText() {
		return $this->textContent;
	}
}