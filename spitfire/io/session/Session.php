<?php namespace spitfire\io\session;

use spitfire\App;
use spitfire\core\Environment;

/**
 * The Session class allows your application to write data to a persistent space
 * that automatically expires after a given time. This class allows you to quickly
 * and comfortably select the persistence mechanism you want and continue working.
 * 
 * This class is a <strong>singleton</strong>. I've been working on reducing the
 * amount of single instance objects inside of spitfire, but this class is somewhat
 * special. It represents a single and global resource inside of PHP and therefore
 * will only make the system unstable by allowing several instances.
 */
class Session
{
	
	/**
	 * The session handler is in charge of storing the data to disk once the system
	 * is done reading it.
	 *
	 * @var SessionHandler
	 */
	private $handler;
	
	/**
	 * The Session allows the application to maintain a persistence across HTTP
	 * requests by providing the user with a cookie and maintaining the data on 
	 * the server. Therefore, you can consider all the data you read from the 
	 * session to be safe because it stems from the server.
	 * 
	 * You need to question the fact that the data actually belongs to the same
	 * user, since this may not be guaranteed all the time.
	 * 
	 * @param SessionHandler $handler
	 */
	protected function __construct(SessionHandler$handler = null) {
		$lifetime = 2592000;
		
		if (!$handler) { $handler = new FileSessionHandler(realpath(session_save_path()), $lifetime); }
		
		$this->handler = $handler;
	}
	
	public function getHandler() {
		return $this->handler;
	}
	
	public function setHandler($handler) {
		$this->handler = $handler;
		$this->handler->attach();
		return $this;
	}
		
	public function set($key, $value, $app = null) {
		if ($app === null) {$app = current_context()->app;}
		/* @var $app App */
		$namespace = ($app->getNameSpace())? $app->getNameSpace() : '*';

		if (!session_id()) { $this->start(); }
		$_SESSION[$namespace][$key] = $value;

	}

	public function get($key, $app = null) {
		if ($app === null) {$app = current_context()->app;}
		$namespace = ($app->getNameSpace())? $app->getNameSpace() : '*';

		if (!session_id()) { $this->start(); }
		return isset($_SESSION[$namespace][$key])? $_SESSION[$namespace][$key] : null;

	}

	public function lock($userdata, App$app = null) {
		
		$user = Array();
		$user['ip']       = $_SERVER['REMOTE_ADDR'];
		$user['userdata'] = $userdata;
		$user['secure']   = true;

		$this->set('_SF_Auth', $user, $app);

	}

	public function isSafe(App$app = null) {

		$user = $this->get('_SF_Auth', $app);
		if ($user) {
			$user['secure'] = $user['secure'] && ($user['ip'] == $_SERVER['REMOTE_ADDR']);

			$this->set('_SF_Auth', $user, $app);
			return $user['secure'];
		} 
		else return false;

	}

	public function getUser(App$app = null) {

		$user = $this->get('_SF_Auth', $app);
		return $user? $user['userdata'] : null;
		
	}

	public function start() {
		if (session_id()) { return; }
		$this->handler->attach();
		session_start();
	}
	
	public function destroy() {
		$this->start();
		return session_destroy();
	}
	
	/**
	 * This class requires to be managed in "singleton" mode, since there can only
	 * be one session handler for the system.
	 * 
	 * @staticvar Session $instance
	 * @return Session
	 */
	public static function getInstance() {
		static $instance = null;
		
		if ($instance !== null) { return $instance; }
		
		$handler = Environment::get('session.handler')? : new FileSessionHandler(spitfire()->getCWD() . DIRECTORY_SEPARATOR . SESSION_SAVE_PATH);
		return $instance = new Session($handler);
	}

}
