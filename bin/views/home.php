<?php

use spitfire\View;

class homeView extends View
{
	function __construct($app) {
		parent::__construct($app);
		$this->set('title', 'Default title');
	}
}