<?php

namespace home\test;

use Controller;

class testController extends Controller
{
	
	public function index() {
		echo 'Hi! Hi!';
		ob_flush();
		die();
	}
	
}