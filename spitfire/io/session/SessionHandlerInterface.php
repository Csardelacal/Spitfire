<?php namespace spitfire\io\session;

if (interface_exists('\SessionHandlerInterface')) {
	interface SessionHandlerInterface extends \SessionHandlerInterface {}
}
else {
	interface SessionHandlerInterface {}
}

