<?php

use spitfire\View;

class homeView extends View
{
	function __construct() {
		parent::__construct();
		$this->set('title', 'Default title');
	}
}