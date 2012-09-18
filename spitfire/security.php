<?php

include ('security_io_sanitization.php');


//TODO: (Re)move this functions and stuff


class browser
{
	
	const TYPE_UNKNOWN = 'unknown';
	const TYPE_DESKTOP = 'desktop';
	const TYPE_MOBILE = 'mobile';
	const TYPE_SEBOT = 'search engine';
	
	static $oss = Array(
		'windows'   => self::TYPE_DESKTOP,
		'macintosh' => self::TYPE_DESKTOP,
		'linux'     => self::TYPE_DESKTOP,
		'ios'       => self::TYPE_MOBILE,
		'ipod'      => self::TYPE_MOBILE,
		'ipad'      => self::TYPE_MOBILE,
		'android'   => self::TYPE_MOBILE,
		'googlebot' => self::TYPE_SEBOT,
		'msnbot'    => self::TYPE_SEBOT
		);
	
	static $browsers = Array('firefox', 'chrome', 'safari', 'msie', 'opera');
	static $uas;
	
	static $os;
	static $browser;
	static $version;
	static $type;
	
	static protected function determineBrowser() {
		self::$uas =  strtolower($_SERVER['HTTP_USER_AGENT']);
		foreach (self::$oss as $os => $type) {
			if (strpos(self::$uas, $os) !== false) {
				self::$os = $os;
				self::$type = $type;
				break;
			}
		}
		foreach (self::$browsers as $browser) {
			if ( ($t = strpos(self::$uas, $browser)) !== false) {
				self::$browser = $browser;
				//Try to catch the version // "xxx Firefox/11" Makes: 4 + length(Firefox) + length(/)[1]
				$t+= strlen($browser); 
				$t++;
				$str = substr(self::$uas, $t);
				$v = doubleval($str);
				if ($v) self::$version = $v;
				break;
			}
		}
		
		if (!self::$browser || !self::$os) {
			self::$browser = 'undefined';
			self::$os      = 'undefined';
			self::$type    = TYPE_UNKNOWN;
			log_to_file('logs/unknown_agents.log', 'Unknown user agent: '.self::$uas, true);
		}
	}
	
	static public function get ($value) {
		if (!self::$os && !self::$browser && !self::$type) self::determineBrowser(); 
		switch ($value) {
			case 'os':
				return self::$os;
				break;
			case 'browser':
				return self::$browser;
				break;
			case 'type':
				return self::$type;
				break;
			case 'version':
				return self::$version;
				break;
		}
	}
}